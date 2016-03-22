<?php namespace Dtkahl\ArrayTools;

class CollectionTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var Collection
   */
  public $collection;

  private $_0 = ['first_name' => 'john', 'last_name' => 'smith', 'age' => 44];
  private $_1 = ['first_name' => 'kara', 'last_name' => 'trace', 'age' => 27];
  private $_2 = ['first_name' => 'phil', 'last_name' => 'mcKay', 'age' => 34];
  private $_3 = ['first_name' => 'rose', 'last_name' => 'smith', 'age' => 27];


  public function setUp()
  {
    $this->collection = new Collection([$this->_0, $this->_1, $this->_2, $this->_3]);
  }

  public function testToArray()
  {
    $this->assertCount(4, $this->collection->toArray());
  }

  public function testCount()
  {
    $this->assertEquals(4, $this->collection->count());
  }

  public function testToJson()
  {
    $this->assertEquals(
        '[{"first_name":"john","last_name":"smith","age":44},{"first_name":"kara","last_name":"trace","age":27},{"first_name":"phil","last_name":"mcKay","age":34},{"first_name":"rose","last_name":"smith","age":27}]',
        $this->collection->toJson()
    );
  }

  public function testcopy()
  {
    $copy = $this->collection->copy();
    $copy->push('test');
    $this->assertEquals(4, $this->collection->count());
    $this->assertEquals(5, $copy->count());
  }

  public function testHasKeyRemoveClearIndexes()
  {
    $collection = $this->collection->copy();
    $this->assertEquals(true, $collection->hasKey(3));
    $collection->remove(3);
    $this->assertEquals(false, $collection->hasKey(3));
  }

  public function testIsEmptyClear()
  {
    $collection = $this->collection->copy();
    $this->assertEquals(false, $collection->isEmpty());
    $collection->clear();
    $this->assertEquals(true, $collection->isEmpty());
  }

  public function testFirst()
  {
    $el = $this->collection->first();
    $this->assertEquals($this->_0, $el);
  }

  public function testLast()
  {
    $el = $this->collection->last();
    $this->assertEquals($this->_3, $el);
  }

  public function testGet()
  {
    $el = $this->collection->get(2);
    $this->assertEquals($this->_2, $el);
  }

  public function testEach()
  {
    $this->collection->each(function ($item)
    {
      $this->assertArrayHasKey('first_name', $item);
      $this->assertArrayHasKey('last_name', $item);
      $this->assertArrayHasKey('age', $item);
    });
  }

  public function testFilter()
  {
    $copy = $this->collection->copy()->filter(function ($item) {
      return $item['last_name'] == 'smith';
    });
    $this->assertEquals(2, $copy->count());
    $this->assertContains($this->_0, $copy->toArray());
    $this->assertNotContains($this->_1, $copy->toArray());
    $this->assertNotContains($this->_2, $copy->toArray());
    $this->assertContains($this->_3, $copy->toArray());
  }

  public function testReverse()
  {
    $copy = $this->collection->copy()->reverse();
    $this->assertEquals($this->_3, $copy->get(0));
    $this->assertEquals($this->_2, $copy->get(1));
    $this->assertEquals($this->_1, $copy->get(2));
    $this->assertEquals($this->_0, $copy->get(3));
  }

  public function testPopPushPutShiftUnshiftInject()
  {
    $copy = $this->collection->copy();

    // pop
    $_3 = $copy->pop();
    $this->assertEquals($this->_3, $_3);
    $this->assertEquals($this->_2, $copy->last());
    $this->assertEquals(3, $copy->count());

    // push
    $copy->push($_3);
    $this->assertEquals($this->_3, $copy->last());

    // put
    $copy->put(2, 'test');
    $this->assertEquals('test', $copy->get(2));

    // shift
    $_0 = $copy->shift();
    $this->assertEquals($this->_0, $_0);
    $this->assertEquals($this->_1, $copy->first());
    $this->assertEquals(3, $copy->count());

    // unshift
    $copy->unshift($_0);
    $this->assertEquals($this->_0, $copy->first());

    // inject
    $copy->inject(2, 'test2');
    $this->assertEquals('test2', $copy->get(2));
    $this->assertEquals(5, $copy->count());
  }

  public function testMergeArray()
  {
    $copy = $this->collection->copy()->merge(['test']);
    $this->assertEquals(5, $copy->count());
    $this->assertEquals('test', $copy->last());
  }

  public function testMergeCollection()
  {
    $copy = $this->collection->copy()->merge(new Collection(['test']));
    $this->assertEquals(5, $copy->count());
    $this->assertEquals('test', $copy->last());
  }

  public function testSort()
  {
    $copy = $this->collection->copy()->sort(function ($a, $b) {
      return $a['age'] >= $b['age'];
    });
    $this->assertEquals([$this->_1, $this->_3, $this->_2, $this->_0], $copy->toArray());
  }

  public function testMap()
  {
    $copy = $this->collection->copy()->map(function ($item) {
      return sprintf('%s %s', $item['first_name'], $item['last_name']);
    });
    $this->assertEquals(['john smith', 'kara trace', 'phil mcKay', 'rose smith'], $copy->toArray());
  }

  public function testChunk()
  {
    /**
     * @var Collection[] $chunks
     */
    $chunks = $this->collection->chunk(2);
    $this->assertCount(2, $chunks);
    $this->assertEquals($this->_0, $chunks[0]->get(0));
    $this->assertEquals($this->_1, $chunks[0]->get(1));
    $this->assertEquals($this->_2, $chunks[1]->get(0));
    $this->assertEquals($this->_3, $chunks[1]->get(1));
  }

  public function testPointerIteration()
  {
    $this->assertEquals($this->_0, $this->collection->next());
    $this->assertEquals($this->_1, $this->collection->next());
    $this->assertEquals($this->_2, $this->collection->next());
    $this->assertEquals($this->_3, $this->collection->next());
    $this->assertEquals(null, $this->collection->next());
    $this->assertEquals($this->_3, $this->collection->previous());
    $this->assertEquals($this->_2, $this->collection->previous());
    $this->assertEquals($this->_1, $this->collection->previous());
    $this->assertEquals($this->_0, $this->collection->previous());
    $this->assertEquals(null, $this->collection->previous());
    $this->collection->setPointer(2);
    $this->assertEquals($this->_2, $this->collection->current());

  }

  public function testLists()
  {
    $list = $this->collection->copy()->lists(['first_name', 'last_name']);
    $this->assertEquals([['first_name' => 'john', 'last_name' => 'smith'], ['first_name' => 'kara', 'last_name' => 'trace'], ['first_name' => 'phil', 'last_name' => 'mcKay'], ['first_name' => 'rose', 'last_name' => 'smith']], $list);
  }

}