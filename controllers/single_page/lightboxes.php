<?php

namespace Concrete\Package\BrandCentral\Controller\SinglePage;

use Carbon\Carbon;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\EntryList;
use Concrete\Core\File\Service\File;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Page\Page;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Express;
use Concrete5\BrandCentral\Entity\AssetDownload;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use ZipArchive;

class Lightboxes extends PageController
{
    /** @var \Concrete\Core\Http\ResponseFactory */
    protected $responseFactory;

    /** @var File */
    protected $file;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var User */
    protected $user;

    public function __construct(
        Page $c,
        File $fileService,
        LoggerInterface $logger,
        EntityManagerInterface $entityManager,
        ResponseFactory $responseFactory,
        User $user

    ) {
        parent::__construct($c);
        $this->responseFactory = $responseFactory;
        $this->file = $fileService;
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->user = $user;
    }

    public function view($lightboxID = null)
    {
        $express = $this->app->make('express');
        $lightboxObj = $express->getObjectByHandle('lightbox');
        $list = new EntryList($lightboxObj);
        $list->sortByDateAddedDescending();

        $lightbox = Express::getEntry($lightboxID);

        if ($lightbox && $lightbox->getEntity()->getHandle() == 'lightbox') {
            $this->set('lightbox', $lightbox);
            $this->set('lightbox_assets', (array)$lightbox->getAssets());
            $this->set('lightbox_name', ucwords($lightbox->getLightboxName()));
            $this->set('lightboxes', $list->getResults());
        } else {
            $this->set('lightboxes', $list->getResults());
        }

    }

    public function download($lightboxID)
    {
        //Checks if user is registered
        $u = new User();
        if (!$u->isRegistered()) {
            return $this->responseFactory->forbidden(t('You must be a registered user.'));
        }

        $lightbox = Express::getEntry($lightboxID);

        if ($lightbox && $lightbox->getEntity()->getHandle() == 'lightbox') {

            $filename = $this->file->getTemporaryDirectory() . '/' . md5('do_download_' . $lightboxID) . '.zip';
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
            foreach ($lightbox->getAssets() as $asset) {
                $files = $asset->getAssetFiles();
                foreach ($files as $file) {
                    $assetFile = $file->getAssetFile();
                    $zip->addFromString($assetFile->getFilename(), $assetFile->getFileContents());
                }

                $this->trackDownload($asset);
            }

            $zip->close();

            $response = new BinaryFileResponse($filename);
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                snake_case('brandcentral_lightbox_' . str_replace(' ', '_', $lightbox->getLightboxName()) . '.zip')
            );
            return $response;

        } else {
            return $this->responseFactory->notFound(t('Lightbox not found.'));
        }
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
}
