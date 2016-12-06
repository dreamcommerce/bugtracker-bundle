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

    /**
     * @return bool
     */
    public function isUseCounter();

    /**
     * @param bool $useCounter
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setUseCounter($useCounter);

    /**
     * @return null|int
     */
    public function getCounterMaxValue();

    /**
     * @param int|null $counterMaxValue
     *
     * @return $this
     */
    public function setCounterMaxValue($counterMaxValue = null);
}
