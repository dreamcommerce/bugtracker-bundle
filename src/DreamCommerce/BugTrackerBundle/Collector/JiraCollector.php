<?php

namespace DreamCommerce\BugTrackerBundle\Collector;

use DreamCommerce\BugTrackerBundle\Exception\RuntimeException;
use DreamCommerce\BugTrackerBundle\Jira\Client as JiraClient;
use DreamCommerce\BugTrackerBundle\Jira\Configuration as JiraConfiguration;
use DreamCommerce\BugTrackerBundle\Jira\Issue;
use Psr\Log\LogLevel;
use Symfony\Component\Debug\Exception\ContextErrorException;

class JiraCollector extends BaseCollector
{
    /**
     * @var JiraClient
     */
    private $_jiraClient;

    /**
     * @var JiraConfiguration
     */
    private $_jiraConfiguration;

    /**
     * @param JiraClient $jiraClient
     * @param JiraConfiguration $jiraConfiguration
     * @param array $options
     */
    public function __construct(JiraClient $jiraClient, JiraConfiguration $jiraConfiguration, array $options = array())
    {
        $this->_jiraClient = $jiraClient;
        $this->_jiraConfiguration = $jiraConfiguration;

        parent::__construct($options);
    }

    /**
     * @return JiraClient
     */
    public function getJiraClient()
    {
        if($this->_jiraClient === null) {
            throw new RuntimeException('Jira client has been not specified');
        }

        return $this->_jiraClient;
    }

    /**
     * @param JiraClient $jiraClient
     * @return $this
     */
    public function setJiraClient(JiraClient $jiraClient)
    {
        $this->_jiraClient = $jiraClient;

        return $this;
    }

    /**
     * @return JiraConfiguration
     */
    public function getJiraConfiguration()
    {
        if($this->_jiraConfiguration === null) {
            throw new RuntimeException('Jira configuration has been not specified');
        }

        return $this->_jiraConfiguration;
    }

    /**
     * @param JiraConfiguration $jiraConfiguration
     * @return $this
     */
    public function setJiraConfiguration(JiraConfiguration $jiraConfiguration)
    {
        $this->_jiraConfiguration = $jiraConfiguration;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _handle($exc, $level = LogLevel::WARNING, array $context = array())
    {
        $issue = null;
        $client = $this->getJiraClient();
        $configuration = $this->getJiraConfiguration();

        if (!$configuration->isUseHash()) {
            $hash = $this->_getJiraHash($exc, $level, $context);
            $issue = $client->getIssueByField($configuration, 'hash', $hash);
            if ($issue !== null) {
                if ($configuration->isUseCounter()) {
                    $counterField = 'customfield_' . $issue->getCounterFieldId();
                    if ($result['fields'][$counterField] < $this->getMaxCounter()) {
                        $client->persistIssue($result['id'], ++$result['fields'][$counterField]);
                    }
                } else {
                    $this->_isCollected = true;
                    return;
                }
            }
        }

        if(!$issue === null) {
            $issue = $this->_createJiraIssue($exc, $level, $context);
        }
        $client->persistIssue($configuration, $issue);
        $this->_isCollected = true;
    }


    protected function _createJiraIssue($exc, $level, array $context = array())
    {
        return new Issue(array(
            // TODO
        ));
    }

    /**
     * @param \Exception|\Throwable $exc
     * @param int                   $level
     * @param array                 $context
     *
     * @return string
     */
    protected function _getJiraSummary($exc, $level, array $context = array())
    {
        $message = substr($exc->getMessage(), 0, 200).' (code: '.$exc->getCode().')';
        if (!($exc instanceof ContextErrorException)) {
            $message = get_class($exc).': '.$message;
        }

        return substr($message, 0, 255);
    }

    /**
     * @param \Exception|\Throwable $exc
     * @param int                   $level
     * @param array                 $context
     *
     * @return string
     */
    protected function _getJiraDescription($exc, $level, array $context = array())
    {
        return substr(
            $exc->getMessage().' (code: '.$exc->getCode().')'.PHP_EOL.PHP_EOL.
            $exc->getTraceAsString(),
            0, 4000
        );
    }

    /**
     * @param \Exception|\Throwable $exc
     * @param int                   $level
     * @param array                 $context
     *
     * @return string
     */
    protected function _getJiraHash($exc, $level, array $context = array())
    {
        $hashParams = array();
        foreach ($this->_getJiraHashParams($exc, $level, $context) as $paramName) {
            if (isset($context[$paramName])) {
                $hashParams[$paramName] = $context[$paramName];
            }
        }

        if (count($hashParams) > 0) {
            ksort($hashParams);
            $hash = md5(serialize($hashParams));
        } else {
            $hash = md5(uniqid(rand(), true));
        }

        return $hash;
    }

    /**
     * @param \Exception|\Throwable $exc
     * @param int                   $level
     * @param array                 $context
     *
     * @return array
     */
    protected function _getJiraHashParams($exc, $level, array $context = array())
    {
        return array('message', 'code', 'line', 'file');
    }
}
