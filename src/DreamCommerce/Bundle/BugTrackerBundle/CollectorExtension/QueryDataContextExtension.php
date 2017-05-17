<?php
namespace DreamCommerce\Bundle\BugTrackerBundle\Collector\Extension;


use DreamCommerce\Component\BugTracker\Collector\Extension\ContextCollectorExtensionInterface;
use Symfony\Component\HttpFoundation\Request;

class QueryDataContextExtension extends RequestStackBasedExtension implements ContextCollectorExtensionInterface
{
    public function getAdditionalContext(\Throwable $throwable): array
    {
        /** @var Request $request */
        $request = $this->requestStack->getMasterRequest();

        if ($request === null) {
            return ['b'];
        }

        return [
            'query' => $request->query->all()
        ];
    }
}