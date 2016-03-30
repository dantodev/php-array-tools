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

  public function testCopy()
  {
    $copy = $this->collection->copy();
    $copy->push('test');
    $this->assertEquals(4, $this->collection->count());
    $this->assertEquals(5, $copy->count());
  }

  public function testHasKeyRemoveClearIndexes()
  {
    $this->assertEquals(true, $this->collection->hasKey(3));
    $this->collection->remove(3);
    $this->assertEquals(false, $this->collection->hasKey(3));
  }

  public function testIsEmptyClear()
  {
    $this->assertEquals(false, $this->collection->isEmpty());
    $this->collection->clear();
    $this->assertEquals(true, $this->collection->isEmpty());
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
    $arr = [];
    $this->collection->each(function ($item, $key) use (&$arr)
    {
      $arr[$key] = $item;
    });
    $this->assertEquals([$this->_0, $this->_1, $this->_2, $this->_3], $arr);
  }

  public function testFilter()
  {
    $this->collection->filter(function ($item) {
      return $item['last_name'] == 'smith';
    });
    $this->assertEquals(2, $this->collection->count());
    $this->assertContains($this->_0, $this->collection->toArray());
    $this->assertNotContains($this->_1, $this->collection->toArray());
    $this->assertNotContains($this->_2, $this->collection->toArray());
    $this->assertContains($this->_3, $this->collection->toArray());
  }

  public function testReverse()
  {
    $this->collection->reverse();
    $this->assertEquals($this->_3, $this->collection->get(0));
    $this->assertEquals($this->_2, $this->collection->get(1));
    $this->assertEquals($this->_1, $this->collection->get(2));
    $this->assertEquals($this->_0, $this->collection->get(3));
  }

  public function testPopPushPutShiftUnshiftInject()
  {
    // pop
    $_3 = $this->collection->pop();
    $this->assertEquals($this->_3, $_3);
    $this->assertEquals($this->_2, $this->collection->last());
    $this->assertEquals(3, $this->collection->count());

    // push
    $this->collection->push($_3);
    $this->assertEquals($this->_3, $this->collection->last());

    // put
    $this->collection->put(2, 'test');
    $this->assertEquals('test', $this->collection->get(2));

    // shift
    $_0 = $this->collection->shift();
    $this->assertEquals($this->_0, $_0);
    $this->assertEquals($this->_1, $this->collection->first());
    $this->assertEquals(3, $this->collection->count());

    // unshift
    $this->collection->unshift($_0);
    $this->assertEquals($this->_0, $this->collection->first());

    // inject
    $this->collection->inject(2, 'test2');
    $this->assertEquals('test2', $this->collection->get(2));
    $this->assertEquals(5, $this->collection->count());
  }

  public function testMergeArray()
  {
    $this->collection->merge(['test']);
    $this->assertEquals(5, $this->collection->count());
    $this->assertEquals('test', $this->collection->last());
  }

  public function testMergeCollection()
  {
    $this->collection->merge(new Collection(['test']));
    $this->assertEquals(5, $this->collection->count());
    $this->assertEquals('test', $this->collection->last());
  }

  public function testSort()
  {
    $this->collection->sort(function ($a, $b) {
      if (preg_match('#^7.#', phpversion())) {
        return $a['age'] > $b['age'];
      } else {
        return $a['age'] >= $b['age'];
      }
    });
    $this->assertEquals([$this->_1, $this->_3, $this->_2, $this->_0], $this->collection->toArray());
  }

  public function testMap()
  {
    $this->collection->map(function ($item) {
      return sprintf('%s %s', $item['first_name'], $item['last_name']);
    });
    $this->assertEquals(['john smith', 'kara trace', 'phil mcKay', 'rose smith'], $this->collection->toArray());
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
    $list = $this->collection->lists(['first_name', 'last_name']);
    $this->assertEquals([['first_name' => 'john', 'last_name' => 'smith'], ['first_name' => 'kara', 'last_name' => 'trace'], ['first_name' => 'phil', 'last_name' => 'mcKay'], ['first_name' => 'rose', 'last_name' => 'smith']], $list);
  }

}