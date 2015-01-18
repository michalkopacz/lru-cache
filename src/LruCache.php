<?php

namespace MostSignificantBit\LruCache;

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

    /**
     * 
     * @param int $size
     * @throws \InvalidArgumentException
     */
    public function __construct($size)
    {
        if (!is_int($size) || (int)$size <= 0) {
            throw new \InvalidArgumentException('Size must be positive integer');
        }

        $this->size = (int)$size;
    }

    /**
     * Retrieve an value for specified key.
     * 
     * @param int|string $key
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function get($key, $default = null)
    {
        $this->checkKeyIsValid($key);
        
        if (!array_key_exists($key, $this->data)) {
            return $default;
        }

        $value = $this->data[$key];

        $this->changeKeyToLastUsed($key, $value);

        return $value;
    }

    /**
     * Add value of specified key.
     * If key exist, it repleace value.
     * 
     * @param int|string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->checkKeyIsValid($key);

        if (array_key_exists($key, $this->data)) {
            $this->changeKeyToLastUsed($key, $value);
            return;
        }

        if ($this->isLimitReached()) {
            $this->removeEarliestUsedKey();
        }

        $this->data[$key] = $value;
    }

    /**
     * Remove value of specified key.
     * 
     * @param int|string $key
     */
    public function remove($key)
    {
        unset($this->data[$key]);
    }

    /**
     * Clean all cached data.
     */
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
    
    /**
     * 
     * @param mixed $key
     * @throws \InvalidArgumentException
     */
    protected function checkKeyIsValid($key)
    {
        if (!is_string($key) && !is_int($key)) {
            throw new \InvalidArgumentException('Only string and integer is valid key type');
        }
    }
}