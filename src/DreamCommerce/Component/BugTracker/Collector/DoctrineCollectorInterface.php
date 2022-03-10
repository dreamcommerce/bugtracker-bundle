<?php

/**
 * (c) 2017-2020 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Collector;

use Doctrine\Persistence\ObjectManager;
use DreamCommerce\Component\Common\Exception\NotDefinedException;
use InvalidArgumentException;

interface DoctrineCollectorInterface extends CollectorInterface
{
    /**
     * @throws NotDefinedException
     *
     * @return ObjectManager
     */
    public function getPersistManager(): ObjectManager;

    /**
     * @param ObjectManager $persistManager
     *
     * @return $this
     */
    public function setPersistManager(ObjectManager $persistManager);

    /**
     * @throws NotDefinedException
     *
     * @return string
     */
    public function getModel(): string;

    /**
     * @param string $model
     *
     * @return $this
     */
    public function setModel(string $model);

    /**
     * @throws NotDefinedException
     *
     * @return \Sylius\Component\Resource\Factory\FactoryInterface
     */
    public function getModelFactory(): \Sylius\Component\Resource\Factory\FactoryInterface;

    /**
     * @param \Sylius\Component\Resource\Factory\FactoryInterface $modelFactory
     * @return $this
     */
    public function setModelFactory(\Sylius\Component\Resource\Factory\FactoryInterface $modelFactory);

    /**
     * @return bool
     */
    public function isUseCounter(): bool;

    /**
     * @param bool $useCounter
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setUseCounter(bool $useCounter);

    /**
     * @return null|int
     */
    public function getCounterMaxValue();

    /**
     * @param int|null $counterMaxValue
     *
     * @return $this
     */
    public function setCounterMaxValue(int $counterMaxValue = null);
}
