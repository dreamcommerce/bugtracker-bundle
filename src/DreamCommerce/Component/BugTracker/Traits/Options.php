<?php

namespace DreamCommerce\Component\BugTracker\Traits;

trait Options
{
    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options = array())
    {
        foreach ($options as $option => $value) {
            $option = ucfirst($option);
            $funcName = 'set'.$option;
            if (method_exists($this, $funcName)) {
                call_user_func(array($this, $funcName), $value);
                continue;
            }

            $camelCase = str_replace(' ', '', ucwords(str_replace('_', ' ', $option)));
            $funcName = 'set'.$camelCase;
            if (method_exists($this, $funcName)) {
                call_user_func(array($this, $funcName), $value);
                continue;
            }

            if (property_exists($this, $option)) {
                $this->$camelCase = $value;
                continue;
            }

            if (property_exists($this, '_'.$option)) {
                $this->$camelCase = $value;
                continue;
            }

            $camelCase = lcfirst($camelCase);
            if (property_exists($this, $camelCase)) {
                $this->$camelCase = $value;
                continue;
            }

            $camelCase = '_'.$camelCase;
            if (property_exists($this, $camelCase)) {
                $this->$camelCase = $value;
                continue;
            }
        }

        return $this;
    }
}
