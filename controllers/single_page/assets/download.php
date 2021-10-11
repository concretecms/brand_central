<?php

namespace Concrete\Package\BrandCentral\Controller\SinglePage\Assets;

use Carbon\Carbon;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Express\ObjectManager;
use Concrete\Core\File\Service\File;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Session\SessionValidator;
use Concrete\Core\User\PostLoginLocation;
use Concrete\Core\User\User;
use Concrete\Core\Utility\Service\Identifier;
use Concrete\Core\Validation\CSRF\Token;
use Doctrine\ORM\EntityManagerInterface;
use Concrete5\BrandCentral\Entity\AssetDownload;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\Session;
use ZipArchive;

class Download extends PageController
{

    private const ENABLE_DOWNLOAD_INTERSTITIAL = true;

    /** @var File */
    protected $file;

    /** @var ObjectManager */
    protected $express;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /** @var \Concrete\Core\Error\ErrorList\ErrorList */
    protected $error;

    /** @var \Concrete\Core\Validation\CSRF\Token */
    protected $token;

    /** @var \Concrete\Core\Http\ResponseFactory */
    protected $responseFactory;

    /** @var Identifier */
    protected $identifier;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var User */
    protected $user;

    protected $sessionValidator;

    protected $session;

    public function __construct(
        Page $c,
        File $fileService,
        ObjectManager $express,
        Identifier $identifier,
        LoggerInterface $logger,
        ErrorList $error,
        Token $token,
        ResponseFactory $responseFactory,
        EntityManagerInterface $entityManager,
        SessionValidator $sessionValidator,
        User $user
    )
    {
        parent::__construct($c);
        $this->file = $fileService;
        $this->express = $express;
        $this->logger = $logger;
        $this->error = $error;
        $this->token = $token;
        $this->responseFactory = $responseFactory;
        $this->identifier = $identifier;
        $this->entityManager = $entityManager;
        $this->user = $user;
        $this->sessionValidator = $sessionValidator;
        $this->session = $this->sessionValidator->getActiveSession();
    }

    /**
     * Base view function for our download singlepage
     *
     * @param string $assetId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view(string $assetId = ''): ?SymfonyResponse
    {
        $this->checkPermissions();

        // Check constant, if disabled just pass through to the do_download url
        if (!self::ENABLE_DOWNLOAD_INTERSTITIAL) {
            return $this->do_download($assetId, $this->token->generate('do_download_' . $assetId));
        }

        // Make sure we have a valid asset object
        if (!$asset = $this->getAsset((int)$assetId)) {
            return $this->responseFactory->notFound(t('Asset not found.'));
        }

        // Make sure we have permission to download this file
        $checker = $this->app->make(Checker::class, [$asset]);
        if (!$checker->canViewExpressEntry()) {
            return $this->responseFactory->forbidden($this->request->getPath());
        }

        $this->set('asset', $asset);
        $this->set('downloadUrl', $this->getDownloadUrl($asset));
        return null;
    }

    public function checkPermissions()
    {
        $u = new User();

        if (!($u->isRegistered() || ($this->session instanceof Session && $this->session->has("download_opt_in")))) {
            $this->setupRequestActionAndParameters($this->request);
            $parameters = $this->getRequestActionParameters();
            $assetID = intval($parameters[0]);
            /**
             * @var $helper PostLoginLocation
             */
            $this->responseFactory->forbidden(\URL::to('/assets', $assetID))->send();
            $this->app->shutdown();
        }
    }

    /**
     * This method is not integrated in the frontend because it's a non-EU project, but if you need to make this
     * project GDPR-compliant you can add this URL to the disclaimer to allow user's removing their download
     * opt-in permissions.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|SymfonyResponse
     */
    public function opt_out()
    {
        if ($this->session instanceof Session) {
            $this->session->remove("download_opt_in");
        }

        return $this->responseFactory->json(["success" => true]);
    }

    public function opt_in()
    {
        if ($this->session instanceof Session) {
            $this->session->set("download_opt_in", true);
        }

        return $this->responseFactory->json(["success" => true]);
    }

    /**
     * Handle actually triggering the download of a file
     *
     * @param string $assetId
     * @param string $token
     *
     * @return null|\Symfony\Component\HttpFoundation\Response
     */
    public function do_download(string $assetId = '', string $token = ''): ?SymfonyResponse
    {
        $this->checkPermissions();

        // Double check the token
        if (!$this->token->validate('do_download_' . $assetId, $token)) {
            return new Response('', Response::HTTP_FORBIDDEN);
        }

        // Make sure we have a valid asset object
        if (!$asset = $this->getAsset((int)$assetId)) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        // Make sure we actually have permission to download it
        $checker = $this->app->make(Checker::class, [$asset]);
        if (!$checker->canViewExpressEntry()) {
            return new Response('', Response::HTTP_FORBIDDEN);
        }

        $zip = $this->resolveZipFile($asset);
        if (!$zip) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        $this->trackDownload($asset);

        // Return the download response
        $response = new BinaryFileResponse($zip);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            snake_case('brandcentral_' . $asset->getAssetName() . '.zip')
        );
        return $response;
    }

    /**
     * Get an asset from an ID
     *
     * @param int $assetId
     *
     * @return \Concrete\Core\Entity\Express\Entry
     */
    protected function getAsset(int $assetId): ?Entry
    {
        // Make sure we are given goo input
        if ($assetId <= 0) {
            $this->error->add(t('Invalid asset ID.'));
            return null;
        }

        /** @var Entry $entry */
        $entry = $this->express->getEntry($assetId);

        // Make sure we have a valid result
        if (!$entry || !$entry->is('asset')) {
            $this->error->add(t('Asset not found.'));
            return null;
        }

        return $entry;
    }

    /**
     * Get the download URL to an asset
     *
     * @param \Concrete\Core\Entity\Express\Entry $asset
     *
     * @return string
     */
    protected function getDownloadUrl(Entry $asset): string
    {
        $id = $asset->getID();
        return $this->action('do_download', $id, $this->token->generate('do_download_' . $id));
    }

    /**
     * Track a download in the database
     *
     * @param \Concrete\Core\Entity\Express\Entry $asset
     */
    protected function trackDownload(Entry $asset): void
    {
        $download = (new AssetDownload())
            ->setAssetId($asset->getID())
            ->setUtcDate(Carbon::now('utc'));

        if ($this->user->checkLogin()) {
            $download->setUserId($this->user->getUserID());
        }

        $this->entityManager->persist($download);
        $this->entityManager->flush();
        $this->entityManager->detach($download);
    }

    /**
     * Resolve the actual zip file we want to offer for download
     *
     * @TODO Memoize this output so that we're not building zip files every single time
     *
     * @param \Concrete\Core\Entity\Express\Entry $asset
     *
     * @return null|string
     */
    private function resolveZipFile(Entry $asset): ?string
    {
        // Create a temporary file
        $filename = $this->file->getTemporaryDirectory() . '/' . md5('do_download_' . $asset->getID()) . '.zip';
        $directory = dirname($filename);

        if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
            $this->logger->critical('Unable to create temp zip directory.');
            return null;
        }

        // Build a new zip for every download
        $zip = new ZipArchive();

        if ($zip->open($filename, ZipArchive::CREATE) !== true) {
            $this->logger->critical('Unable to open temp file for zip download.');
            return null;
        }

        // Get the asset files and add them to the zip
        $files = $asset->getAssetFiles();

        foreach ($files as $file) {
            $assetFile = $file->getAssetFile();
            $zip->addFromString(DIR_BASE . $assetFile->getFilename(), $assetFile->getFileContents());
        }

        // If we actually have files in the zip
        if ($zip->numFiles && $zip->close()) {
            return $filename;
        }

        return null;
    }
}
