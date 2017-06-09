<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Tests\BugTrackerBundle\Collector\Extension;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

trait RequestStackMockTrait
{

    /** @param array           $query      The GET parameters
     * @param array           $request    The POST parameters
     * @param array           $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array           $cookies    The COOKIE parameters
     * @param array           $files      The FILES parameters
     * @param array           $server     The SERVER parameters
     */
    /**
     * Get RequestStack with one Request inside. Request has got sample date get by function from this interface
     *
     * @return RequestStack
     */
    public function getRequestStack(): RequestStack
    {
        $request = new Request(
            $this->getQueryMockData(),
            $this->getRequestMockData(),
            array(), //Attributes
            array(), //Cookies
            array(), //Files
            $this->getServerMockData()
        );


        $requestStack = new RequestStack();
        $requestStack->push($request);

        return $requestStack;
    }

    /**
     *  Get sample params for $request->query
     *
     * @return array
     */
    public function getQueryMockData(): array
    {
        return array(
            'query_param1' => 'value_for_query_param1',
            'query_param2' => 'value_for_query_param2',
            'query_param3' => 'value_for_query_param3',
            'query_param4' => 'value_for_query_param4',
            'query_param5' => 'value_for_query_param5',
        );
    }

    /**
     * Get sample params for $request->request
     *
     * @return array
     */
    public function getRequestMockData(): array
    {
        return array(
            'request_param1' => 'value_for_request_param1',
            'request_param2' => 'value_for_request_param2',
            'request_param3' => 'value_for_request_param3',
            'request_param4' => 'value_for_request_param4',
            'request_param5' => 'value_for_request_param5'
        );
    }

    /**
     *  Get sample params for $request->server
     *
     * @return array
     */
    public function getServerMockData(): array
    {
        return array(
            'REMOTE_ADDR' => '127.0.0.1'
        );
    }
}
