<?php

declare(strict_types=1);

namespace Concrete5\AssetLibrary\Metadata;

/**
 * Represents the metadata extracted by one of our extractors
 *
 * @readonly
 */
class FileMetadata
{
    public ?string $title;
    public ?string $description;
    public array $keywords;

    /**
     * @param string $title
     * @param string $description
     * @param string[] $keywords
     */
    public function __construct(?string $title, ?string $description, array $keywords)
    {
        $this->title = $title;
        $this->description = $description;
        $this->keywords = $keywords;
    }
}