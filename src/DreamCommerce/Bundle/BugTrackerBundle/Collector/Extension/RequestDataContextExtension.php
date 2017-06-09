<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Bundle\BugTrackerBundle\Collector\Extension;

use Symfony\Component\HttpFoundation\Request;

class RequestDataContextExtension extends RequestStackBasedExtension
{
    public function getAdditionalContext(\Throwable $throwable): array
    {
        /** @var Request $request */
        $request = $this->requestStack->getMasterRequest();

        if ($request === null) {
            return array();
        }

        return array(
            'request' => $request->request->all()
        );
    }
}
