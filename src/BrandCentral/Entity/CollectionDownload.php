<?php
namespace Concrete5\BrandCentral\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CollectionDownloadRepository")
 * @ORM\Table(name="CollectionDownloadStatistics", indexes={@ORM\Index(name="collectionIndex", columns={"collectionId", "userId"})})
 */
class CollectionDownload
{

    /**
     * @ORM\Id @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     * @var string
     */
    protected $downloadId;

    /**
     * @ORM\Column(type="datetime")
     * @var Datetime
     */
    protected $utcDate;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $collectionId;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int|null
     */
    protected $userId = null;

    /**
     * @return string
     */
    public function getDownloadId(): string
    {
        return $this->downloadId;
    }

    /**
     * @param string $downloadId
     * @return CollectionDownload
     */
    public function setDownloadId(string $downloadId): CollectionDownload
    {
        $this->downloadId = $downloadId;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUtcDate(): DateTime
    {
        return $this->utcDate;
    }

    /**
     * @param \DateTime $utcDate
     * @return CollectionDownload
     */
    public function setUtcDate(DateTime $utcDate): CollectionDownload
    {
        $this->utcDate = $utcDate;
        return $this;
    }

    /**
     * @return int
     */
    public function getCollectionId(): int
    {
        return $this->collectionId;
    }

    /**
     * @param int $collectionId
     * @return CollectionDownload
     */
    public function setCollectionId(int $collectionId): CollectionDownload
    {
        $this->collectionId = $collectionId;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     * @return CollectionDownload
     */
    public function setUserId(int $userId): CollectionDownload
    {
        $this->userId = $userId;
        return $this;
    }
}
