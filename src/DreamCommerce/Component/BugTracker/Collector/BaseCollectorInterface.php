<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Collector;

use DreamCommerce\Component\Common\Model\ArrayableInterface;

interface BaseCollectorInterface extends CollectorInterface, TokenAwareInterface, ArrayableInterface
{
    /**
     * @return array
     */
    public function getIgnoreExceptions();

    /**
     * @param \Throwable|\Exception|string $exception
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function addIgnoreException($exception);

    /**
     * @param array $ignoreExceptions
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setIgnoreExceptions(array $ignoreExceptions = array());

    /**
     * @return array
     */
    public function getExceptions();

    /**
     * @param \Throwable|\Exception|string $exception
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function addException($exception);

    /**
     * @param array $exceptions
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setExceptions(array $exceptions = array());

    /**
     * @return bool
     */
    public function isLocked();

    /**
     * @return $this
     */
    public function lock();

    /**
     * @return $this
     */
    public function unlock();

    /**
     * @param \Exception|\Throwable $exception
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public function getContext($exception);
}
