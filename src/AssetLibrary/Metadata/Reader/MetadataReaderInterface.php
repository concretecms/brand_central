<?php

declare(strict_types=1);

namespace Concrete5\AssetLibrary\Metadata\Reader;

use Concrete5\AssetLibrary\Metadata\FileMetadata;

interface MetadataReaderInterface
{
    /**
     * Read metadata from a given resource
     * Returns null if no relevant data was found and an empty FileMetadata if data is found but not filled in
     * @param resource $resource
     * @return mixed
     */
    public function read($resource): ?FileMetadata;
}