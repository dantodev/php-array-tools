<?php namespace Dtkahl\ArrayTools;

class PropertyHolderTest extends \PHPUnit_Framework_TestCase
{

  /**
   * @var PropertyHolder
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
    $this->assertArrayHasKey('foo1', $this->properties->all(), true);
  }

  public function testRemove()
  {
    $this->properties->remove('foo1');
    $this->assertEquals(null, $this->properties->get('foo1'));
  }

}