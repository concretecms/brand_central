<?php

namespace Concrete5\BrandCentral\Entity;

use Doctrine\ORM\EntityRepository;

class CollectionDownloadRepository extends EntityRepository
{

    /**
     * Count the amount of downloads of an collection
     *
     * @param int $collectionId
     * @param int $userId
     *
     * @return int >=0
     */
    public function countDownloads(int $collectionId, int $userId = null): int
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('count(downloadId)')->where($qb->expr()->eq('collectionId', ':collectionId'));

        if ($userId) {
            $qb->andWhere($qb->expr()->eq('userId', ':userId'));
        }

        $result = $qb->getQuery()->execute(['collectionId' => $collectionId, 'userId' => $userId]);
        return (int)$result;
    }

}
