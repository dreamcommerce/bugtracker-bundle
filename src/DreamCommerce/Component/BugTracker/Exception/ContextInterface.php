<?php

namespace DreamCommerce\Component\BugTracker\Exception;

interface ContextInterface
{
    /**
     * @return array
     */
    public function getExceptionContext();
}
