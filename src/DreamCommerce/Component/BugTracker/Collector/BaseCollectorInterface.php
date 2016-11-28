<?php

namespace DreamCommerce\Component\BugTracker\Collector;

use DreamCommerce\Component\BugTracker\Exception\InvalidArgumentException;

interface BaseCollectorInterface extends CollectorInterface, TokenAwareInterface
{
    /**
     * @return array
     */
    public function getIgnoreExceptions();

    /**
     * @param \Throwable|\Exception|string $exception
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function addIgnoreException($exception);

    /**
     * @param array $ignoreExceptions
     * @throws InvalidArgumentException
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
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function addException($exception);

    /**
     * @param array $exceptions
     * @throws InvalidArgumentException
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
     * @throws InvalidArgumentException
     *
     * @return array
     */
    public function getContext($exception);
}
