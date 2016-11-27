<?php

namespace DreamCommerce\Component\BugTracker\Collector;

interface BaseCollectorInterface extends CollectorInterface, TokenAwareInterface
{
    /**
     * @return array
     */
    public function getIgnoreExceptions();

    /**
     * @param \Error|\Exception|string $exc
     *
     * @return $this
     */
    public function addIgnoreException($exc);

    /**
     * @param array $ignoreExceptions
     *
     * @return $this
     */
    public function setIgnoreExceptions(array $ignoreExceptions = array());

    /**
     * @return array
     */
    public function getExceptions();

    /**
     * @param \Error|\Exception|string $exc
     *
     * @return $this
     */
    public function addException($exc);

    /**
     * @param array $exceptions
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
     * @param \Exception|\Throwable $exc
     *
     * @return array
     */
    public function getContext($exc);
}
