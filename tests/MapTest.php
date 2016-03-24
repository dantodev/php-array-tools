<?php namespace Dtkahl\ArrayTools;

class MapTest extends \PHPUnit_Framework_TestCase
{

  /**
   * @var Map
   */
  public $properties;

  public function setUp()
  {
    $this->properties = new Map(['foo1' => 'bar1']);
  }

  public function testConstructGet()
  {
    $this->assertEquals('bar1', $this->properties->get('foo1'));
  }

  public function testSetGet()
  {
    $this->properties->set('foo2', 'bar2');
    $this->assertEquals('bar2', $this->properties->get('foo2'));
  }

  public function testGetDefault()
  {
    $this->assertEquals('bar3', $this->properties->get('foo3', 'bar3'));
  }

  public function testHas()
  {
    $this->assertEquals(true, $this->properties->has('foo1'));
    $this->assertEquals(false, $this->properties->has('foo3'));
  }

  public function testAll()
  {
    $this->assertEquals(['foo1' => 'bar1'], $this->properties->all());
  }

  public function testRemove()
  {
    $this->properties->remove('foo1');
    $this->assertEquals(null, $this->properties->get('foo1'));
  }

  public function testGetKeys()
  {
    $this->assertEquals(['foo1'], $this->properties->getKeys());
  }

  public function testMerge()
  {
    $this->properties->merge(['foo4' => 'bar4']);
    $this->assertEquals('bar4', $this->properties->get('foo4'));
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
    $this->assertEquals('{"foo1":"bar1"}', $this->properties->toJson());
  }

}