<?php

/**
 * (c) 2017-2020 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Exception\Jira;

use DreamCommerce\Component\BugTracker\Exception\JiraException;
use Exception;
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
     * @param Exception $previousException
     * @return UnableDecodeResponseException
     */
    public static function forRequest(RequestInterface $request, Exception $previousException = null): UnableDecodeResponseException
    {
        $exception = new static('Unable decode response', static::CODE_FOR_REQUEST, $previousException);
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
