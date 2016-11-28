<?php

namespace DreamCommerce\Component\BugTracker\Collector;

use Doctrine\ORM\EntityManagerInterface;
use DreamCommerce\Component\BugTracker\Exception\NotDefinedException;

interface DoctrineCollectorInterface extends CollectorInterface
{
    /**
     * @throws NotDefinedException
     *
     * @return EntityManagerInterface
     */
    public function getEntityManager();

    /**
     * @param EntityManagerInterface $entityManager
     *
     * @return $this
     */
    public function setEntityManager(EntityManagerInterface $entityManager);

    /**
     * @throws NotDefinedException
     *
     * @return string
     */
    public function getModel();

    /**
     * @param string $model
     *
     * @return $this
     */
    public function setModel($model);
}
