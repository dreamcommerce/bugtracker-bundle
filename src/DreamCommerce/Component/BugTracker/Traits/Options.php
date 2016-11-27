<?php

namespace DreamCommerce\Component\BugTracker\Traits;

use Webmozart\Assert\Assert;

trait Options
{
    /**
     * @param array $options
     * @param object|null $object
     *
     * @return $this
     */
    public function setOptions(array $options = array(), $object = null)
    {
        Assert::nullOrObject($object);

        if($object === null) {
            $object = $this;
        }

        foreach ($options as $option => $value) {
            $option = ucfirst($option);
            $funcName = 'set'.$option;
            if (method_exists($object, $funcName)) {
                call_user_func(array($object, $funcName), $value);
                continue;
            }

            $camelCase = str_replace(' ', '', ucwords(str_replace('_', ' ', $option)));
            $funcName = 'set'.$camelCase;
            if (method_exists($object, $funcName)) {
                call_user_func(array($object, $funcName), $value);
                continue;
            }

            if (property_exists($object, $option)) {
                $object->$camelCase = $value;
                continue;
            }

            if (property_exists($object, '_'.$option)) {
                $object->$camelCase = $value;
                continue;
            }

            $camelCase = lcfirst($camelCase);
            if (property_exists($object, $camelCase)) {
                $object->$camelCase = $value;
                continue;
            }

            $camelCase = '_'.$camelCase;
            if (property_exists($object, $camelCase)) {
                $object->$camelCase = $value;
                continue;
            }
        }

        return $this;
    }
}
