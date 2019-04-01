<?php

namespace Concrete5\AssetLibrary\Results\Formatter;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\File\Image\Thumbnail\Type\Type as ThumbnailType;
use Concrete\Core\File\Type\Type;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Support\Facade\Url;

class Asset
{

    const COLLECTION_SILO_MATERIAL = 'Material';
    const COLLECTION_SILO_PHOTO = 'Photo';

    /**
     * @var Entry
     */
    protected $entry;

    /**
     * @var \Concrete\Core\Entity\File\Image\Thumbnail\Type\Type|null
     */
    protected $thumbnailType;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var string
     */
    protected $thumbnailUrl;

    /**
     * @var string
     */
    protected $detailImageUrl;

    public function __construct(Entry $entry)
    {
        $app = Facade::getFacadeApplication();
        if ($entry->getEntity()->getHandle() != 'asset') {
            throw new \RuntimeException(t('Only asset objects may be passed to this class.'));
        }
        $this->app = $app;
        $this->entry = $entry;
    }

    public function getId()
    {
        return $this->entry->getId();
    }

    public function getName()
    {
        return $this->entry->getAssetName();
    }

    public function getPublicViewLink()
    {
        return Url::to('/assets', $this->entry->getID());
    }

    public function getDateAdded()
    {
        $datetime = $this->entry->getDateCreated();
        if ($datetime) {
            return $datetime->format('F d, Y');
        }
    }

    public function getThumbnailImageURL()
    {
        if (!isset($this->thumbnailUrl)) {
            $image = $this->getImage();
            if ($image) {
                $imageType = ThumbnailType::getByHandle('asset_detail');
                $url = $image->getThumbnailURL($imageType->getBaseVersion());
                if ($url) {
                    $this->thumbnailUrl = $url;
                } else {
                    $this->thumbnailUrl = $image->getURL();
                }
            } else {
                $this->thumbnailUrl = '';
            }
        }
        return $this->thumbnailUrl;
    }

    public function getDetailImageURL()
    {
        if (!isset($this->detailImageUrl)) {
            $image = $this->getImage();
            if ($image) {
                $imageType = ThumbnailType::getByHandle('asset_detail');
                $url = $image->getThumbnailURL($imageType->getBaseVersion());
                if ($url) {
                    $this->detailImageUrl = $url;
                } else {
                    $this->detailImageUrl = $image->getURL();
                }
            } else {
                $this->detailImageUrl = '';
            }
        }
        return $this->detailImageUrl;
    }

    public function getDownloadURL()
    {
        return Url::to('/assets/download', $this->getId());
    }

    public function getImage()
    {
        if (!isset($this->thumbnailImage)) {
            $image = $this->entry->getAssetThumbnail();
            if ($image) {
                $this->thumbnailImage = $image;
            } else {
                // We have not uploaded a custom thumbnail. So let's loop through all asset files til we find one.
                foreach ((array)$this->entry->getAssetFiles() as $assetFile) {
                    $file = $assetFile->getAssetFile();
                    if ($file && $file->getTypeObject()->getGenericType() == Type::T_IMAGE) {
                        $this->thumbnailImage = $file;
                        break;
                    }
                }
            }
        }
        return $this->thumbnailImage;
    }

    public function __call($name, $arguments)
    {
        return $this->entry->$name(...$arguments);
    }

}
