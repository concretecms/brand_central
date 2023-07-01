<?php

namespace Concrete5\AssetLibrary\Results\Formatter;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\UserInfoRepository;
use Doctrine\DBAL\Connection;

class Collection
{

    const COLLECTION_SILO_MATERIAL = 'Material';
    const COLLECTION_SILO_PHOTO = 'Photo';

    /**
     * @var Entry
     */
    protected $entry;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var int
     */
    protected $total;

    /**
     * @var Connection;
     */
    protected $db;

    /**
     * @var Entry[]
     */
    protected $assets = [];

    public function __construct(Entry $entry)
    {
        $app = Facade::getFacadeApplication();
        if ($entry->getEntity()->getHandle() != 'collection') {
            throw new \RuntimeException(t('Only collection objects may be passed to this class.'));
        }
        $this->app = $app;
        $this->entry = $entry;
        $this->db = $app->make(Connection::class);
        $this->assets = (array) $this->entry->getAssets();
    }

    /**
     * @TODO make this work with a core author property
     */
    public function getAuthor()
    {
        $service = $this->app->make(UserInfoRepository::class);
        $user = $service->getByID(1);
        return $user;
    }

    public function getTitle()
    {
        return $this->entry->getCollectionName();
    }

    public function getDescription()
    {
        return $this->entry->getCollectionDescription();
    }


    public function getPublicViewLink()
    {
        return Url::to('/collections', $this->entry->getID());
    }

    public function getDateAdded()
    {
        $datetime = $this->entry->getDateCreated();
        if ($datetime) {
            return $datetime->format('F d, Y');
        }
    }

    public function getTotalAssets()
    {
        if (!isset($this->total)) {
            $assocation = $this->entry->getAssociation('assets');
            if ($assocation) {
                $this->total = $this->db->fetchColumn(
                    'select count(*) from ExpressEntityEntryAssociations where exEntryID = ? and association_id = ?',
                    [$this->entry->getId(), $assocation->getAssociation()->getId()]
                );
            } else {
                $this->total = 0;
            }
        }
        return $this->total;
    }

    public function getContentsDescription()
    {
        $total = count($this->assets);
        if ((string) $this->entry->getCollectionSilo() == self::COLLECTION_SILO_PHOTO) {
            return t2('%s photo', '%s photos', $total);
        } else {
            return t2('%s item', '%s items', $total);
        }
    }

    public function getCoverImageURL()
    {
        $asset = $this->assets[0];
        if ($asset) {
            $asset = new Asset($asset);
            $image = $asset->getDetailImageURL();
            if ($image) {
                return $image;
            }
        }
    }

    public function getThumbnailImageURL()
    {
        $asset = $this->assets[0] ?? null;
        if ($asset) {
            $asset = new Asset($asset);
            $image = $asset->getThumbnailImageURL();
            if ($image) {
                return $image;
            }
        }
    }


    public function getAssets()
    {
        return $this->assets;
    }
}
