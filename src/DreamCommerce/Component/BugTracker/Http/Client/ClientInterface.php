<?php

namespace DreamCommerce\Component\BugTracker\Http\Client;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

interface ClientInterface
{
    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @param array                              $options - Vendor-specific options, don't rely on for interop
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function send(RequestInterface $request, array $options = array());

    /**
     * @param string              $method
     * @param string|UriInterface $uri
     * @param array               $options - Vendor-specific options, don't rely on for interop.
     *
     * Standard compliant client must implement the following options:
     *
     * - "headers" is a list of headers, for example ["Content-Type: application/json"]
     * - "body" contains the HTTP body if sending POST, PUT, ...
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function request($method, $uri, array $options = array());

    /**
     * @param null|string                     $method          HTTP method for the request
     * @param null|string|UriInterface        $uri             URI for the request
     * @param array                           $headers         Headers for the message
     * @param string|resource|StreamInterface $body            Message body
     * @param string                          $protocolVersion HTTP protocol version
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    public function createRequest($method, $uri, array $headers = array(), $body = null, $protocolVersion = '1.1');
    /**
     * @param string $uri - If uri is provided the result of parse_url() is set as parts for UriInterface
     *
     * @throws \InvalidArgumentException When passed $uri is not a string or not parsable uri
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function createUri($uri = null);
    /**
     * @param mixed $data
     *
     * @throws \InvalidArgumentException When passed data cannot be converted to a stream
     *
     * @return \Psr\Http\Message\StreamInterface
     */
    public function createStream($data);
}
