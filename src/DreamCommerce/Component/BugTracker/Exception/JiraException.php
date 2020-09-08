<?php

/**
 * (c) 2017-2020 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Exception;

use DreamCommerce\Component\Common\Exception\ContextInterface;
use DreamCommerce\Component\Common\Exception\ContextTrait;
use Exception;

class JiraException extends Exception implements ContextInterface
{
    use ContextTrait;
}
