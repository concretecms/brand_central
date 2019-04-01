<?php

namespace Concrete5\BrandCentral\Entity;

use Doctrine\ORM\EntityRepository;

class AssetDownloadRepository extends EntityRepository
{

    /**
     * Count the amount of downloads of an asset
     *
     * @param int $assetId
     * @param int $userId
     *
     * @return int >=0
     */
    public function countDownloads(int $assetId, int $userId = null): int
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('count(downloadId)')->where($qb->expr()->eq('assetId', ':assetId'));

        if ($userId) {
            $qb->andWhere($qb->expr()->eq('userId', ':userId'));
        }

        $result = $qb->getQuery()->execute(['assetId' => $assetId, 'userId' => $userId]);
        return (int)$result;
    }

}
