<?php

namespace MostSignificantBit\LruCache\Tests;

use MostSignificantBit\LruCache\LruCache;

class LruCacheTest extends  \PHPUnit_Framework_TestCase
{
    protected $lruCache;

    public function setUp()
    {
        $this->lruCache = new LruCache(3);
    }

    public function testCanSetAndGetValue()
    {
        $this->lruCache->set('foo', 'bar');

        $this->assertSame('bar', $this->lruCache->get('foo'));
    }

    /**
     * @dataProvider invalidKeysProvider
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidKey($key)
    {
        $this->lruCache->set($key, 'bazz');
    }

    public function testCanOverwriteExistingKey()
    {
        $this->lruCache->set('foo', 'bar');
        $this->lruCache->set('foo', 'baz');

        $this->assertSame('baz', $this->lruCache->get('foo'));
    }

    /**
     * @dataProvider invalidCacheSizeProvider
     * @expectedException \InvalidArgumentException
     */
    public function testCreateCacheWithInvalidSize($key)
    {
        new LruCache($key);
    }

    public function testKeyValueIsNotSet()
    {
        $this->assertNull($this->lruCache->get('foo'));
    }

    public function testOverflowCacheRemoveEarliestUsedKey()
    {
        $this->lruCache->set('foo', 1);
        $this->lruCache->set('bar', 2);
        $this->lruCache->set('baz', 3);
        $this->lruCache->set('buz', 4);
   
        $this->assertNull($this->lruCache->get('foo'));
        $this->assertSame(2, $this->lruCache->get('bar'));
        $this->assertSame(3, $this->lruCache->get('baz'));
        $this->assertSame(4, $this->lruCache->get('buz'));

    }

    public function testSetTheSameKeyNotRemoveEarliestUsedKey()
    {
        $this->lruCache->set('foo', 1);
        $this->lruCache->set('baz', 2);
        $this->lruCache->set('bar', 3);
        $this->lruCache->set('baz', 4);

        $this->assertSame(1, $this->lruCache->get('foo'));
        $this->assertSame(4, $this->lruCache->get('baz'));
        $this->assertSame(3, $this->lruCache->get('bar'));
    }

    public function testGetEarliestUsedKeyIsRemoved()
    {
        $this->lruCache->set('foo', 1);
        $this->lruCache->set('bar', 2);
        $this->lruCache->set('baz', 3);

        $this->lruCache->get('foo');

        $this->lruCache->set('buz', 4);

        $this->assertNull($this->lruCache->get('bar'));
    }
    
    public function testArrayAccess()
    {
        $this->lruCache['foo'] = 1;
        
        $this->assertSame(1, $this->lruCache['foo']);
        
        $this->assertTrue(isset($this->lruCache['foo']));
        
        unset($this->lruCache['foo']);
        
        $this->assertFalse(isset($this->lruCache['foo']));
        
        $this->assertNull($this->lruCache->get('foo'));
    }
    
    public function testIssetIsFalseForNullValue()
    {
        $this->lruCache['foo'] = null;
        
        $this->assertFalse(isset($this->lruCache['foo']));
    }

    public function invalidKeysProvider()
    {
        return array(
            array(null),
            array(false),
            array(true),
            array(array()),
            array(new \stdClass()),
            array(1.23),
        );
    }

    public function invalidCacheSizeProvider()
    {
        return array(
            array(null),
            array(false),
            array(true),
            array(array()),
            array(new \stdClass()),
            array(1.23),
            array(0),
            array(-1),
            array(-1234),
        );
    }
}