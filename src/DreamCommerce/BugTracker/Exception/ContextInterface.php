<?php

namespace DreamCommerce\BugTracker\Exception;

interface ContextInterface
{
    /**
     * @return array
     */
    public function getExceptionContext();
}
