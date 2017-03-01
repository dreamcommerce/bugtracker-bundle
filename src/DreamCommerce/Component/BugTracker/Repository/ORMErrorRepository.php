<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Repository;

use Doctrine\ORM\EntityRepository;
use DreamCommerce\Component\BugTracker\Model\ErrorInterface;

class ORMErrorRepository extends EntityRepository implements ErrorRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByToken(string $token)
    {
        $token = iconv('UTF-8', 'ASCII//TRANSLIT', trim(strtoupper($token)));

        /** @var ErrorInterface $entity */
        $entity = $this->findOneBy(array(
            'token' => $token,
        ));

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
