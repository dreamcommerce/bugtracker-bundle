<?php

namespace DreamCommerce\BugTracker\Http\Client;

use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;

class GuzzleWrapper implements ClientInterface
{
    /**
     * @var GuzzleClientInterface
     */
    private $_guzzleClient;

    /**
     * @param GuzzleClientInterface $guzzleClient
     */
    public function __construct(GuzzleClientInterface $guzzleClient)
    {
        $this->_guzzleClient = $guzzleClient;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function send(RequestInterface $request, array $options = array())
    {
        return $this->_guzzleClient->send($request, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function request($method, $uri, array $options = array())
    {
        return $this->_guzzleClient->request($method, $uri, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function createRequest($method, $uri, array $headers = array(), $body = null, $protocolVersion = '1.1')
    {
        return new Request($method, $uri, $headers, $body, $protocolVersion);
    }

    /**
     * {@inheritdoc}
     */
    public function createUri($uri = null)
    {
        return new Uri($uri);
    }

    /**
     * {@inheritdoc}
     */
    public function createStream($data)
    {
        return Psr7\stream_for($data);
    }


}