<?php

namespace Concrete\Package\BrandCentral\Controller\SinglePage\Collections;

use Carbon\Carbon;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Express\ObjectManager;
use Concrete\Core\File\Service\File;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\User\PostLoginLocation;
use Concrete\Core\User\User;
use Concrete\Core\Utility\Service\Identifier;
use Concrete\Core\Validation\CSRF\Token;
use Concrete5\AssetLibrary\Results\Formatter\Asset;
use Doctrine\ORM\EntityManagerInterface;
use Concrete5\BrandCentral\Entity\CollectionDownload;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
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
        User $user
    ) {
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
    }

    /**
     * Base view function for our download singlepage
     *
     * @param string $collectionId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view(string $collectionId = ''): ?SymfonyResponse
    {
        // Check constant, if disabled just pass through to the do_download url
        if (!self::ENABLE_DOWNLOAD_INTERSTITIAL) {
            return $this->do_download($collectionId, $this->token->generate('do_collection_download_' . $collectionId));
        }

        // Make sure we have a valid collection object
        if (!$collection = $this->getCollection((int)$collectionId)) {
            return $this->responseFactory->notFound(t('Collection not found.'));
        }

        // Make sure we have permission to download this file
        $checker = $this->app->make(Checker::class, [$collection]);
        if (!$checker->canViewExpressEntry()) {
            return $this->responseFactory->forbidden($this->request->getPath());
        }

        $this->set('collection', $collection);
        $this->set('downloadUrl', $this->getDownloadUrl($collection));
        return null;
    }

    public function on_start()
    {
        parent::on_start();
        $u = new User();
        if (!$u->isRegistered()) {
            $this->setupRequestActionAndParameters($this->request);
            $parameters = $this->getRequestActionParameters();
            $collectionID = intval($parameters[0]);
            /**
             * @var $helper PostLoginLocation
             */
            return $this->responseFactory->forbidden(\URL::to('/collections', $collectionID));
        }
    }

    /**
     * Handle actually triggering the download of a file
     *
     * @param string $collectionId
     * @param string $token
     *
     * @return null|\Symfony\Component\HttpFoundation\Response
     */
    public function do_download(string $collectionId = '', string $token = ''): ?SymfonyResponse
    {
        // Double check the token
        if (!$this->token->validate('do_collection_download_' . $collectionId, $token)) {
            return new Response('', Response::HTTP_FORBIDDEN);
        }

        // Make sure we have a valid collection object
        if (!$collection = $this->getCollection((int)$collectionId)) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        // Make sure we actually have permission to download it
        $checker = $this->app->make(Checker::class, [$collection]);
        if (!$checker->canViewExpressEntry()) {
            return new Response('', Response::HTTP_FORBIDDEN);
        }

        $zip = $this->resolveZipFile($collection);
        if (!$zip) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        $this->trackDownload($collection);

        // Return the download response
        $response = new BinaryFileResponse($zip);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            snake_case('brandcentral_' . $collection->getCollectionName() . '.zip')
        );
        return $response;
    }

    /**
     * Get an collection from an ID
     *
     * @param int $collectionId
     *
     * @return \Concrete\Core\Entity\Express\Entry
     */
    protected function getCollection(int $collectionId): ?Entry
    {
        // Make sure we are given goo input
        if ($collectionId <= 0) {
            $this->error->add(t('Invalid collection ID.'));
            return null;
        }

        /** @var Entry $entry */
        $entry = $this->express->getEntry($collectionId);

        // Make sure we have a valid result
        if (!$entry || !$entry->is('collection')) {
            $this->error->add(t('Collection not found.'));
            return null;
        }

        return $entry;
    }

    /**
     * Get the download URL to an collection
     *
     * @param \Concrete\Core\Entity\Express\Entry $collection
     *
     * @return string
     */
    protected function getDownloadUrl(Entry $collection): string
    {
        $id = $collection->getID();
        return $this->action('do_download', $id, $this->token->generate('do_collection_download_' . $id));
    }

    /**
     * Track a download in the database
     *
     * @param \Concrete\Core\Entity\Express\Entry $collection
     */
    protected function trackDownload(Entry $collection): void
    {
        $download = (new CollectionDownload())
            ->setCollectionId($collection->getID())
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
     * @param \Concrete\Core\Entity\Express\Entry $collection
     *
     * @return null|string
     */
    private function resolveZipFile(Entry $collection): ?string
    {
        // Create a temporary file
        $filename = $this->file->getTemporaryDirectory() . '/' . md5('do_collection_download_' . $collection->getID()) . '.zip';
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

        // Get all assets from the collection
        $assetsAssociation = $collection->getAssociation("assets");

        if ($assetsAssociation instanceof Entry\ManyAssociation) {
            foreach($assetsAssociation->getSelectedEntries() as $assetEntry) {
                /** @var $assetEntry Entry */
                $assetFilesAssociation = $assetEntry->getAssociation("asset_files");

                if ($assetFilesAssociation instanceof Entry\ManyAssociation) {
                    foreach($assetFilesAssociation->getSelectedEntries() as $assetFileEntry) {
                        /** @var $assetFileEntry Entry */
                        $assetFile = $assetFileEntry->getAttribute("asset_file");

                        if ($assetFile instanceof FileEntity) {
                            $assetFileVersion = $assetFile->getApprovedVersion();

                            if ($assetFileVersion instanceof Version) {
                                $zip->addFile($assetFile->getRelativePath(), $assetFile->getFileName());
                            }
                        }
                    }
                }
            }
        }

        // If we actually have files in the zip
        if ($zip->numFiles && $zip->close()) {
            return $filename;
        }

        return null;
    }
}
