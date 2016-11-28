<?php

namespace DreamCommerce\Bundle\BugTrackerBundle\Doctrine\ORM\Repository;

use Doctrine\ORM\EntityRepository;
use DreamCommerce\Component\BugTracker\Model\ErrorInterface;
use DreamCommerce\Component\BugTracker\Repository\ErrorRepositoryInterface;

class ErrorRepository extends EntityRepository  implements ErrorRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByToken($token)
    {
        $token = iconv('UTF-8', 'ASCII//TRANSLIT', trim(strtoupper($token)));

        /** @var ErrorInterface $entity */
        $entity = $this->findOneBy([
            'token' => $token
        ]);

        return $entity;
    }
}