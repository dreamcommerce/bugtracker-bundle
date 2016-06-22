<?php

namespace DreamCommerce\BugTrackerBundle\Exception;

interface ContextInterface
{
    /**
     * @return array
     */
    public function getExceptionContext();
}
