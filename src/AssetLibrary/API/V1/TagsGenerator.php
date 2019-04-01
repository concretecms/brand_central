<?php

namespace Concrete5\AssetLibrary\API\V1;

use Concrete\Core\File\File;
use Google\Cloud\Vision\VisionClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TagsGenerator
{

    protected $request;
    protected $session;

    private function processImage($url)
    {
        $vision = new VisionClient();
        $imageX = $vision->image($url, ['LABEL_DETECTION']);
        $result = $vision->annotate($imageX);

        $labels = [];

        foreach ($result->labels() as $label) {
            if ($label->info()['score'] >= 0.60) {
                $labels[] = ['id' => $label->info()['mid'], 'name' => $label->info()['description']];
            }
        }

        return $labels;
    }

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function processImages()
    {
        $postFiles = json_decode($this->request->getContent());
        $files = [];

        foreach ($postFiles->files as $file) {
            $img = File::getByID($file);
            $files[] = [
                'size' => $img->getFullSize(),
                'id' => $img->getFileID(),
                'type' => $img->getMimeType(),
                'url' => $img->getURL()
            ];
        }

        $largeImg = max($files);
        $imageURL = file_get_contents($largeImg['url']);

        $vision = new VisionClient();
        $imageX = $vision->image($imageURL, ['LABEL_DETECTION']);
        $result = $vision->annotate($imageX);

        $labels = [];

        foreach ((array)$result->labels() as $label) {
            if ($label->info()['score'] >= 0.60) {
                $labels[] = ['id' => $label->info()['mid'], 'name' => $label->info()['description']];
            }
        }

        return new JsonResponse($labels);
    }

    public function processImageById()
    {
        $file = json_decode($this->request->getContent());

        $img = File::getByID($file->id);
        $imageURL = file_get_contents($img->getURL());

        return new JsonResponse($this->processImage($imageURL));
    }
}
