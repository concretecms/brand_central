<?php

namespace Concrete5\AssetLibrary\API\V1\Transformer;

use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\File\Version;
use Concrete5\AssetLibrary\Results\Formatter\AssetFile;
use League\Fractal\TransformerAbstract;

class AssetFileTransformer extends TransformerAbstract
{

    public function transform(AssetFile $assetFile)
    {
        $downloadUrl = '';
        $originalFileName = '';

        if ($assetFile->getFile() instanceof File) {
            $approvedFileVersion = $assetFile->getFile()->getApprovedVersion();

            if ($approvedFileVersion instanceof Version) {
                $downloadUrl = (string)$approvedFileVersion->getDownloadURL();
                $originalFileName = $approvedFileVersion->getFileName();
            }
        }

        return [
            "downloadUrl" => $downloadUrl,
            "originalFileName" => $originalFileName,
            "assetId" => $assetFile->getAssetId(),
            "description" => $assetFile->getDescription()
        ];
    }


}
