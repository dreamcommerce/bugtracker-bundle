<?php

namespace DreamCommerce\Bundle\BugTrackerBundle\Doctrine\ORM\Repository;

use Doctrine\ORM\EntityRepository;
use DreamCommerce\Component\BugTracker\Model\ErrorInterface;
use DreamCommerce\Component\BugTracker\Repository\ErrorRepositoryInterface;

class ErrorRepository extends EntityRepository implements ErrorRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByToken($token)
    {
        $token = iconv('UTF-8', 'ASCII//TRANSLIT', trim(strtoupper($token)));

        /** @var ErrorInterface $entity */
        $entity = $this->findOneBy([
            'token' => $token,
        ]);

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function incrementCounter(ErrorInterface $entity)
    {
        $em = $this->getEntityManager();
        $className = $em->getClassMetadata(get_class($entity))->getName();
        $query = $em->createQuery('UPDATE '.$className.' t SET t.counter = t.counter + 1');

        $query->getResult();

        return $this;
    }
}
