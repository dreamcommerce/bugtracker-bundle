<?php

namespace DreamCommerce\Component\BugTracker\Generator;

use DreamCommerce\Component\BugTracker\Assert;
use DreamCommerce\Component\BugTracker\Exception\InvalidArgumentException;
use DreamCommerce\Component\BugTracker\Exception\RuntimeException;

class ContextTokenGenerator implements TokenGeneratorInterface
{
    private $_tokenParams = array('message', 'code', 'line', 'file');

    /**
     * {@inheritdoc}
     */
    public function generate($exc, $level, array $context = array())
    {
        $context['message'] = $exc->getMessage();
        $context['code'] = $exc->getCode();
        $context['line'] = $exc->getLine();
        $context['file'] = $exc->getFile();
        $context['level'] = $level;

        $genTokenParams = array();
        foreach ($this->_tokenParams as $paramName) {
            if (isset($context[$paramName])) {
                $genTokenParams[$paramName] = $context[$paramName];
            }
        }

        if (count($genTokenParams) === 0) {
            throw new RuntimeException('Unable generate token from empty context');
        }

        ksort($genTokenParams);
        $hash = md5(serialize($genTokenParams));

        return $hash;
    }

    /**
     * @return array
     */
    public function getTokenParams()
    {
        return $this->_tokenParams;
    }

    /**
     * @param array $tokenParams
     * @throws InvalidArgumentException
     * @return $this
     */
    public function setTokenParams(array $tokenParams = array())
    {
        Assert::notEmpty($tokenParams);

        $this->_tokenParams = $tokenParams;

        return $this;
    }

    /**
     * @param string $tokenParam
     * @throws InvalidArgumentException
     * @return $this
     */
    public function addTokenParam($tokenParam)
    {
        Assert::stringNotEmpty($tokenParam);

        if (!in_array($tokenParam, $this->_tokenParams)) {
            $this->_tokenParams[] = $tokenParam;
        }

        return $this;
    }
}
