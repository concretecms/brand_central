<?php

namespace Concrete5\AssetLibrary\API\V1;

use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\File;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
use Exception;
use Google\Cloud\Vision\VisionClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TagsGenerator
{

    protected $request;
    protected $session;
    /** @var LoggerFactory */
    protected $loggerFactory;
    /** @var LoggerInterface */
    protected $logger;


    public function __construct(
        Request $request,
        LoggerFactory $loggerFactory
    )
    {
        $this->request = $request;
        $this->loggerFactory = $loggerFactory;
        $this->logger = $this->loggerFactory->createLogger(Channels::CHANNEL_PACKAGES);
    }

    private function processImage($url)
    {
        $labels = [];

        try {
            $vision = new VisionClient();
            $imageX = $vision->image($url, ['LABEL_DETECTION']);
            $result = $vision->annotate($imageX);

            foreach ($result->labels() as $label) {
                if ($label->info()['score'] >= 0.60) {
                    $labels[] = ['id' => $label->info()['mid'], 'name' => $label->info()['description']];
                }
            }
        } catch (Exception $error) {
            $this->logger->error($error);
        }

        return $labels;
    }

    public function processImages()
    {
        $postFiles = json_decode($this->request->getContent());
        $files = [];
        $labels = [];

        foreach ($postFiles->files as $file) {
            $img = File::getByID($file);
            $files[] = [
                'size' => $img->getFullSize(),
                'id' => $img->getFileID(),
                'type' => $img->getMimeType(),
                'url' => $img->getURL()
            ];

            $approvedVersion = $img->getApprovedVersion();
            $i = 0;
            if ($approvedVersion instanceof Version) {
                foreach (explode("\n", $approvedVersion->getTags()) as $tag) {
                    if (strlen(trim($tag)) > 0) {
                        $labels[] = [
                            "id" => time() + $i++,
                            "name" => trim($tag)
                        ];
                    }
                }
            }
        }

        $largeImg = max($files);
        $imageURL = file_get_contents($largeImg['url']);

        try {
            $vision = new VisionClient();
            $imageX = $vision->image($imageURL, ['LABEL_DETECTION']);
            $result = $vision->annotate($imageX);

            foreach ((array)$result->labels() as $label) {
                if ($label->info()['score'] >= 0.60) {
                    $labels[] = ['id' => $label->info()['mid'], 'name' => $label->info()['description']];
                }
            }
        } catch (Exception $error) {
            $this->logger->error($error);
        }

        return new JsonResponse($labels);
    }

    public function processImageById()
    {
        $file = json_decode($this->request->getContent());

        $img = File::getByID($file->id);
        $imageURL = file_get_contents($img->getURL());

        // fetch from google vision
        $labels = $this->processImage($imageURL);

        $approvedVersion = $img->getApprovedVersion();

        if ($approvedVersion instanceof Version) {
            $i = 0;

            foreach (explode("\n", $approvedVersion->getTags()) as $tag) {
                if (strlen(trim($tag)) > 0) {
                    $labels[] = [
                        "id" => time() + $i++,
                        "name" => trim($tag)
                    ];
                }
            }
        }

        return new JsonResponse($labels);
    }
}
