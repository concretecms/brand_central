<?php
namespace Concrete5\BrandCentral\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AssetDownloadRepository")
 * @ORM\Table(name="AssetDownloadStatistics", indexes={@ORM\Index(name="assetIndex", columns={"assetId", "userId"})})
 */
class AssetDownload
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
    protected $assetId;

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
     * @return AssetDownload
     */
    public function setDownloadId(string $downloadId): AssetDownload
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
     * @return AssetDownload
     */
    public function setUtcDate(DateTime $utcDate): AssetDownload
    {
        $this->utcDate = $utcDate;
        return $this;
    }

    /**
     * @return int
     */
    public function getAssetId(): int
    {
        return $this->assetId;
    }

    /**
     * @param int $assetId
     * @return AssetDownload
     */
    public function setAssetId(int $assetId): AssetDownload
    {
        $this->assetId = $assetId;
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
     * @return AssetDownload
     */
    public function setUserId(int $userId): AssetDownload
    {
        $this->userId = $userId;
        return $this;
    }
}
