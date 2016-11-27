<?php

namespace DreamCommerce\Component\BugTracker\Collector;

use Doctrine\ORM\EntityManagerInterface;

interface DoctrineCollectorInterface extends CollectorInterface
{
    /**
     * @return EntityManagerInterface|null
     */
    public function getEntityManager();

    /**
     * @param EntityManagerInterface|null $entityManager
     * @return $this
     */
    public function setEntityManager(EntityManagerInterface $entityManager = null);

    /**
     * @return string|null
     */
    public function getModel();

    /**
     * @param string|null $model
     * @return $this
     */
    public function setModel($model);
}