<?php

namespace MichalKopacz\LruCache;

/**
 * This cache use php array linked list functionality to keep keys in the order of use
 *
 * @author Michał Kopacz
 */
class LruCache
{
    protected $size;

    /**
     * Element at the end of array is last used, at the beginning is earliest used.
     *
     * @var array
     */
    protected $data = array();

    public function __construct($size)
    {
        if (!is_int($size) || (int)$size <= 0) {
            throw new \InvalidArgumentException('Size must be positive integer');
        }

        $this->size = (int)$size;
    }

    public function get($key)
    {
        if (!array_key_exists($key, $this->data)) {
            throw new \InvalidArgumentException('Key not exists');
        }

        $value = $this->data[$key];

        $this->changeKeyToLastUsed($key, $value);

        return $value;
    }

    public function set($key, $value)
    {
        if (!is_string($key) && !is_int($key)) {
            throw new \InvalidArgumentException('Only string and integer is valid key type');
        }

        if (array_key_exists($key, $this->data)) {
            $this->changeKeyToLastUsed($key, $value);
            return;
        }

        if ($this->isLimitReached()) {
            $this->removeEarliestUsedKey();
        }

        $this->data[$key] = $value;
    }

    public function remove($key)
    {
        unset($this->data[$key]);
    }

    public function clean()
    {
        $this->data = array();
    }

    /**
     * Remove key from beginning of array.
     */
    protected function removeEarliestUsedKey()
    {
        array_shift($this->data);
    }

    /**
     * Move key to end of array.
     *
     * @param mixed $key
     * @param mixed $value
     */
    protected function changeKeyToLastUsed($key, $value)
    {
        unset($this->data[$key]);

        $this->data[$key] = $value;
    }

    protected function isLimitReached()
    {
        return count($this->data) >= $this->size;
    }
}