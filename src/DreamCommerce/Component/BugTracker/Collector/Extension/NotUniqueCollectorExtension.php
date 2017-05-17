<?php
namespace DreamCommerce\Component\BugTracker\Collector\Extension;


use DreamCommerce\Component\Common\Exception\NotUniqueException;

class NotUniqueCollectorExtension extends NotUniqueException
{
    const CODE_NOT_UNIQUE_EXTENSION = 0xEE;
    /**
     * @var string
     */
    protected $extensionName;


    /**
     * @param string $extensionName
     * @param \Throwable $previousException
     * @return NotUniqueCollectorExtension
     */
    public static function forExtension(string $extensionName, \Throwable $previousException = null): NotUniqueCollectorExtension
    {
        $exception = new static('The parameter must be unique', static::CODE_NOT_UNIQUE_EXTENSION, $previousException);
        $exception->extensionName = $extensionName;

        return $exception;
    }

    /**
     * @return string
     */
    public function getExtensionName(): string
    {
        return $this->extensionName;
    }


}