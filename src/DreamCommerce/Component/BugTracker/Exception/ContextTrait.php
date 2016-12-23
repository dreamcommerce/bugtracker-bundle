<?php

namespace DreamCommerce\Component\BugTracker\Exception;

trait ContextTrait
{
    /**
     * @var array
     */
    protected $context = array();

    /**
     * @return array
     */
    public function getExceptionContext()
    {
        return $this->context;
    }

    /**
     * @param array $context
     * @return $this
     */
    public function setExceptionContext(array $context = array())
    {
        $this->context = $context;

        return $this;
    }
}
