<?php
namespace DreamCommerce\Bundle\BugTrackerBundle\Collector\Extension;

use Symfony\Component\HttpFoundation\Request;

class RequestDataContextExtension extends RequestStackBasedExtension
{
    public function getAdditionalContext(\Throwable $throwable): array
    {
        /** @var Request $request */
        $request = $this->requestStack->getMasterRequest();

        if ($request === null) {
            return [];
        }

        return [
            'request' => $request->request->all()
        ];
    }
}