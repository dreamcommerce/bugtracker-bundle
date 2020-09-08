<?php

/**
 * (c) 2017-2020 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Collector;

use DreamCommerce\Component\BugTracker\Collector\Extension\CollectorExtendable;
use DreamCommerce\Component\Common\Model\ArrayableInterface;
use InvalidArgumentException;
use Throwable;

interface BaseCollectorInterface extends CollectorInterface, TokenAwareInterface, ArrayableInterface, CollectorExtendable
{
    /**
     * @return array
     */
    public function getIgnoreExceptions(): array;

    /**
     * @param Throwable|string $exception
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function addIgnoreException($exception);

    /**
     * @param array $ignoreExceptions
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setIgnoreExceptions(array $ignoreExceptions = array());

    /**
     * @return array
     */
    public function getExceptions(): array;

    /**
     * @param Throwable|string $exception
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function addException($exception);

    /**
     * @param array $exceptions
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setExceptions(array $exceptions = array());

    /**
     * @return bool
     */
    public function isLocked(): bool;

    /**
     * @return $this
     */
    public function lock();

    /**
     * @return $this
     */
    public function unlock();

    /**
     * @param Throwable $exception
     *
     * @throws InvalidArgumentException
     *
     * @return array
     */
    public function getContext(Throwable $exception): array;
}
