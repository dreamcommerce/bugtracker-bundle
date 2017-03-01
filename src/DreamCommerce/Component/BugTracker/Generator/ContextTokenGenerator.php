<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Generator;

use DreamCommerce\Component\BugTracker\Exception\UnableGenerateTokenException;
use InvalidArgumentException;
use Throwable;
use Webmozart\Assert\Assert;

class ContextTokenGenerator implements TokenGeneratorInterface
{
    private $_tokenParams = array('message', 'code', 'line', 'file');

    /**
     * {@inheritdoc}
     */
    public function generate(Throwable $exception, int $level, array $context = array()): string
    {
        $context['message'] = $exception->getMessage();
        $context['code'] = $exception->getCode();
        $context['line'] = $exception->getLine();
        $context['file'] = $exception->getFile();
        $context['level'] = $level;

        $genTokenParams = array();
        foreach ($this->_tokenParams as $paramName) {
            if (isset($context[$paramName])) {
                $genTokenParams[$paramName] = $context[$paramName];
            }
        }

        if (count($genTokenParams) === 0) {
            throw UnableGenerateTokenException::forEmptyContext();
        }

        ksort($genTokenParams);
        $hash = md5(serialize($genTokenParams));

        return $hash;
    }

    /**
     * @return array
     */
    public function getTokenParams(): array
    {
        return $this->_tokenParams;
    }

    /**
     * @param array $tokenParams
     *
     * @throws InvalidArgumentException
     *
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
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function addTokenParam(string $tokenParam)
    {
        Assert::stringNotEmpty($tokenParam);

        if (!in_array($tokenParam, $this->_tokenParams)) {
            $this->_tokenParams[] = $tokenParam;
        }

        return $this;
    }
}
