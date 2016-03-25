<?php namespace Dtkahl\ArrayTools;

class MapTest extends \PHPUnit_Framework_TestCase
{

  /**
   * @var Map
   */
  public $properties;

  public function setUp()
  {
    $this->properties = new Map([
        'foo1' => 'bar1',
        'foo2' => 'bar2'
    ]);
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
    $this->assertEquals(true, $this->properties->has('foo1'));
    $this->assertEquals(false, $this->properties->has('foo4'));
  }

  public function testToArray()
  {
    $this->assertEquals(['foo1' => 'bar1', 'foo2' => 'bar2'], $this->properties->toArray());
  }

  public function testRemove()
  {
    $this->properties->remove('foo1');
    $this->assertEquals(null, $this->properties->get('foo1'));
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
    $map = new Map(['foo6' => 'bar6'], true);
    $success = false;
    try {
      $map->set('foo7', 'bar7');
    } catch (\RuntimeException $e) {
      $success = true;
    }
    $this->assertTrue($success);
  }

}