<?php

namespace DreamCommerce\Component\BugTracker\Collector;

use DreamCommerce\Component\BugTracker\Connector\JiraConnectorInterface;

interface JiraCollectorInterface extends CollectorInterface
{
    /**
     * @return JiraConnectorInterface|null
     */
    public function getConnector();

    /**
     * @param JiraConnectorInterface $connector
     * @return $this
     */
    public function setConnector(JiraConnectorInterface $connector);
}
