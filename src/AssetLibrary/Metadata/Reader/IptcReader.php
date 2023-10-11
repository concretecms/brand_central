<?php

declare(strict_types=1);

namespace Concrete5\AssetLibrary\Metadata\Reader;

use Concrete5\AssetLibrary\Metadata\FileMetadata;

class IptcReader implements MetadataReaderInterface
{
    public function read($resource): ?FileMetadata
    {
        $data = fread($resource, 20 * 1024 * 1024);
        if (substr($data, 0, 2) === 'II') {
            $mime = 'image/tiff';
        } else {
            $mime = 'image/jpeg';
        }

        getimagesize('data://' . $mime . ';base64,' . base64_encode($data), $info);
        unset($data);
        if (empty($info['APP13'])) {
            // No IPTC data found
            return null;
        }

        $iptc = iptcparse($info['APP13']);
        $title = $iptc['2#005'] ?? null;
        $description = $iptc['2#120'] ?? null;
        $keywords = $iptc['2#025'] ?? null;

        return new FileMetadata(
            $title ? reset($title) : null,
            $description ? reset($description) : null,
            $keywords ?: []
        );
    }
}