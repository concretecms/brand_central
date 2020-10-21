<?php

namespace Concrete5\AssetLibrary\Results\Formatter;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\File\File;
use Concrete\Core\File\Image\Thumbnail\Type\Type as ThumbnailType;
use Concrete\Core\File\Type\Type;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Support\Facade\Url;

class AssetFile
{


    /**
     * @var Entry
     */
    protected $entry;


    /**
     * @var Application
     */
    protected $app;

    public function __construct(Entry $entry)
    {
        $app = Facade::getFacadeApplication();

        if ($entry->getEntity()->getHandle() != 'asset_file') {
            throw new \RuntimeException(t('Only asset file objects may be passed to this class.'));
        }

        $this->app = $app;
        $this->entry = $entry;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->entry->getAttribute("asset_file");
    }

    public function getDescription()
    {
        return (string)$this->entry->getAttribute("asset_file_description");
    }


}