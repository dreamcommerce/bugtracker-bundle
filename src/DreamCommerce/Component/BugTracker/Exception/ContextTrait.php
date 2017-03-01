<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

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
    public function getExceptionContext(): array
    {
        return $this->context;
    }

    /**
     * @param array $context
     *
     * @return $this
     */
    public function setExceptionContext(array $context = array())
    {
        $this->context = $context;

        return $this;
    }
}
