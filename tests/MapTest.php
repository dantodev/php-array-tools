<?php namespace Dtkahl\ArrayTools;

class MapTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Map
     */
    public $properties;

    public function testIstance()
    {
        $this->assertTrue($this->properties instanceof Map);
        $this->assertTrue($this->properties instanceof \Countable);
        $this->assertTrue($this->properties instanceof \ArrayAccess);
    }

    public function testArrayAccess()
    {
        $this->assertEquals('bar2', $this->properties['foo2']);
        $this->properties['foo3'] = 'bar3';
        $this->assertTrue(isset($this->properties['foo3']));
        unset($this->properties['foo1']);
        $this->assertFalse(isset($this->properties['foo1']));

    }

    public function setUp()
    {
        $this->properties = new Map([
            'foo1' => 'bar1',
            'foo2' => 'bar2'
        ]);
    }

    public function testMagic()
    {
        $this->assertTrue(isset($this->properties->foo1));
        $this->assertFalse(isset($this->properties->foo3));
        $this->properties->foo3 = "bar3";
        $this->assertEquals("bar3", $this->properties->foo3);
        $this->assertTrue(isset($this->properties->foo3));
    }

    public function testConstructGet()
    {
        $this->assertEquals('bar1', $this->properties->get('foo1'));
    }

    public function testSetGet()
    {
        $this->properties->set('foo3', 'bar3');
        $this->assertEquals('bar3', $this->properties->get('foo3'));
    }

    public function testGetDefault()
    {
        $this->assertEquals('bar4', $this->properties->get('foo4', 'bar4'));
    }

    public function testHas()
    {
        $this->assertTrue($this->properties->has('foo1'));
        $this->assertFalse($this->properties->has('foo4'));
    }

    public function testHasKeys()
    {
        $this->assertTrue($this->properties->hasKeys(['foo1', 'foo2']));
        $this->assertFalse($this->properties->hasKeys(['foo1', 'foo4']));
    }

    public function testToArray()
    {
        $this->assertEquals(['foo1' => 'bar1', 'foo2' => 'bar2'], $this->properties->toArray());
    }

    public function testRemove()
    {
        $this->properties->remove('foo1');
        $this->assertNull($this->properties->get('foo1'));
    }

    public function testGetKeys()
    {
        $this->assertEquals(['foo1', 'foo2'], $this->properties->getKeys());
    }

    public function testMerge()
    {
        $this->properties->merge(['foo5' => 'bar5']);
        $this->assertEquals('bar5', $this->properties->get('foo5'));
    }

    public function testCopy()
    {
        $copy = $this->properties->copy();
        $copy->set('foo5', 'bar5');
        $this->assertEquals('bar5', $copy->get('foo5'));
        $this->assertEquals(null, $this->properties->get('foo5'));
    }

    public function testToJson()
    {
        $this->assertEquals('{"foo1":"bar1","foo2":"bar2"}', $this->properties->toJson());
    }

    public function testExcept()
    {
        $this->assertEquals(['foo2' => 'bar2'], $this->properties->except(['foo1'])->toArray());
    }

    public function testOnly()
    {
        $this->assertEquals(['foo1' => 'bar1'], $this->properties->only(['foo1'])->toArray());
    }

    public function testClear()
    {
        $this->assertEquals([], $this->properties->clear()->toArray());
    }

    public function testKeysLocked()
    {
        $map = new Map(['foo6' => 'bar6'], ['keys_locked' => true]);
        $success = false;
        try {
            $map->set('foo7', 'bar7');
        } catch (\RuntimeException $e) {
            $success = true;
        }
        $this->assertTrue($success);
        $this->assertTrue($map->remove('foo6')->has('foo6'));
        $this->assertEquals('bar6', $map->set('foo6', 'bar6')->get('foo6'));
        $this->assertEquals(['foo6' => null], $map->clear()->toArray());
    }

    public function testCountIsEmpty()
    {
        $this->assertEquals(2, $this->properties->count());
        $this->assertFalse($this->properties->isEmpty());
        $this->properties->clear();
        $this->assertEquals(0, $this->properties->count());
        $this->assertTrue($this->properties->isEmpty());
    }


    public function testEach()
    {
        $arr = [];
        $this->properties->each(function ($key, $item) use (&$arr) {
            $arr[$key] = $item;
        });
        $this->assertEquals([
            'foo1' => 'bar1',
            'foo2' => 'bar2'
        ], $arr);
    }


    public function testMap()
    {
        $this->properties->map(function ($key, $item) {
            return "mapped_".$item;
        });
        $this->assertEquals([
            'foo1' => 'mapped_bar1',
            'foo2' => 'mapped_bar2'
        ], $this->properties->toArray());
    }

    public function testRecursiveOption()
    {
        $map = new Map([
            'persons' => ['Luke', 'Lea'],
            'other' => ['foo' => 'bar', 'foo2' => 'bar2']
        ], ['recursive' => true]);
        $this->assertTrue($map->get('persons') instanceof Collection);
        $this->assertTrue($map->get('other') instanceof Map);
    }

    public function testGetType()
    {
        $this->assertEquals('string', $this->properties->getType('foo1'));
    }

}