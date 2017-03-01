<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Collector;

use Doctrine\Common\Persistence\ObjectManager;
use DreamCommerce\Component\Common\Exception\NotDefinedException;
use InvalidArgumentException;
use Sylius\Component\Resource\Factory\FactoryInterface;

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
     * @return FactoryInterface
     */
    public function getModelFactory(): FactoryInterface;

    /**
     * @param FactoryInterface $modelFactory
     * @return $this
     */
    public function setModelFactory(FactoryInterface $modelFactory);

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
