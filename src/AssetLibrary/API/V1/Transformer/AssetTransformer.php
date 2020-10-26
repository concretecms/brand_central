<?php

namespace Concrete5\AssetLibrary\API\V1\Transformer;

use Concrete\Core\Support\Facade\Facade;
use League\Fractal\TransformerAbstract;
use Concrete5\AssetLibrary\Results\Formatter\Asset;

class AssetTransformer extends TransformerAbstract
{

    protected $defaultIncludes = [
        'tags',
        'collections',
    ];

    public function transform(Asset $asset)
    {

        $assetFiles = [];
        foreach ((array)$asset->getAssetFiles() as $file) {
            $assetFiles[] = [
                'id' => $file->getId(),
                'desc' => $file->getAssetFileDescription(),
                'img' => $file->getAssetFile()->getRelativePath(),
                'filename' => $file->getAssetFile()->getFilename(),
                'isLoading' => false,
                'fid' => $file->getAssetFile()->getFileID()
            ];
        }

        $type = null;

        $assetType = $asset->getAssetType();
        if (is_object($assetType)) {
            $options = $assetType->getSelectedOptions();
            if ($options) {
                $values = $options->getValues();
                if ($values) {
                    $type = $values[0]->getSelectAttributeOptionValue();
                }
            }
        }
        $assetImage = $asset->getAssetThumbnail();
        if ($assetImage) {
            $thumbnail = $assetImage->getURL();
            $thumbnailId = $assetImage->getFileID();
        }

        //React breaks if location and description is null, therefor an empty string is needed to be sent if null.
        $location = $asset->getAssetLocation();
        if (!isset($location)) {
            $location = '';
        }

        $desc = $asset->getAssetDescription();
        if (!isset($desc)) {
            $desc = '';
        }

        $app = Facade::getFacadeApplication();
        $site = $app->make('site')->getSite();
        $url = $site->getSiteCanonicalURL();
        if ($url) {
            $thumbnail = $url . $asset->getThumbnailImageURL();
        } else {
            $thumbnail = $asset->getThumbnailImageURL();
        }
        $assetArr = [
            'id' => $asset->getId(),
            'name' => $asset->getAssetName(),
            'desc' => $desc,
            'location' => $location,
            'type' => $type,
            'thumbnail' => $thumbnail,
            'thumbnailId' => $thumbnailId,
            'files' => $assetFiles,
        ];

        return $assetArr;
    }

    protected function includeTags(Asset $asset)
    {
        $tags = $asset->getAssetTags();
        if (!$tags) {
            $tags = [];
        }
        return $this->collection($tags, new TagTransformer());
    }

    protected function includeCollections(Asset $asset)
    {
        return $this->collection((array)$asset->getCollections(), new CollectionTransformer());
    }

}
