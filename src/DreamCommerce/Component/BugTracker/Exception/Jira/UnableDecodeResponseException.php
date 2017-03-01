<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Exception\Jira;

use DreamCommerce\Component\BugTracker\Exception\JiraException;
use Psr\Http\Message\RequestInterface;

class UnableDecodeResponseException extends JiraException
{
    const CODE_FOR_REQUEST = 10;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param RequestInterface $request
     * @return UnableDecodeResponseException
     */
    public static function forRequest(RequestInterface $request): UnableDecodeResponseException
    {
        $exception = new static('Unable decode response', static::CODE_FOR_REQUEST);
        $exception->request = $request;

        return $exception;
    }

    /**
     * @return RequestInterface|null
     */
    public function getRequest()
    {
        return $this->request;
    }
}
