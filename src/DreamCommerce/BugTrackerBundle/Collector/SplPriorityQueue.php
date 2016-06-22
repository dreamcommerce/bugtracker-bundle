<?php

namespace DreamCommerce\BugTrackerBundle\Collector;

use DreamCommerce\BugTrackerBundle\Exception\InvalidArgumentException;

class SplPriorityQueue extends \SplPriorityQueue
{
    protected $_recoverList = array();

    public function remove($item)
    {
        if (is_object($item)) {
            if (!($item instanceof CollectorInterface)) {
                throw new InvalidArgumentException('Cannot delete the item from the queue. Invalid type of object [ expected: DreamCommerce\BugTrackerBundle\Collector\CollectorInterface; got: '.get_class($item).' ]');
            }
        } elseif (!is_string($item)) {
            throw new InvalidArgumentException('Cannot delete the item from the queue');
        }

        $this->setExtractFlags(self::EXTR_BOTH);
        foreach ($this as $em) {
            if (is_object($item)) {
                if ($item === $em['data']['collector']) {
                    continue;
                }
            } elseif ($item == get_class($em['data']['collector'])) {
                continue;
            }

            $this->_recoverList[] = $em;
        }

        $this->setExtractFlags(self::EXTR_DATA);
        foreach ($this->_recoverList as $em) {
            $this->insert($em['data'], $em['priority']);
        }
        $this->_recoverList = array();
    }

    public function toArray()
    {
        $array = array();
        foreach (clone $this as $item) {
            $array[] = $item;
        }

        return $array;
    }
}