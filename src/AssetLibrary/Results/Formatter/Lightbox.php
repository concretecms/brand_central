<?php

namespace Concrete5\AssetLibrary\Results\Formatter;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Url\Url;
use Concrete\Core\User\UserInfoRepository;
use Doctrine\DBAL\Connection;

class Lightbox
{


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
        if ($entry->getEntity()->getHandle() != 'lightbox') {
            throw new \RuntimeException(t('Only lightbox objects may be passed to this class.'));
        }
        $this->app = $app;
        $this->entry = $entry;
        $this->db = $app->make(Connection::class);
        $this->assets = (array) $this->entry->getAssets();
    }

    public function getId()
    {
        return $this->entry->getId();
    }

    public function getName()
    {
        return $this->entry->getLightboxName();
    }

    public function getAuthor()
    {
        if($this->entry->getAuthor()) {
            return $this->entry->getAuthor()->getUserName();
        }
    }

    public function getDateAdded()
    {
        $datetime = $this->entry->getDateCreated();
        if ($datetime) {
            return $datetime->format('F d, Y');
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

    public function getPublicViewLink()
    {
        return Url::to('/lightboxes', $this->entry->getID());
    }

    public function getMyAccountViewLink()
    {
        return Url::to('/account/lightboxes', $this->entry->getID());
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

    public function __call($name, $arguments)
    {
        return $this->entry->$name(...$arguments);
    }


}
