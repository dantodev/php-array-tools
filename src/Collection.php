<?php namespace Dtkahl\ArrayTools;

class Collection
{

  private $_data = [];
  private $_pointer = -1;

  /**
   * Collection constructor.
   * @param array $data
   */
  public function __construct(array $data = [])
  {
    $this->_data = $data;
    $this->_clearIndexes();
  }

  /**
   * @return array
   */
  public function toArray()
  {
    return $this->_data;
  }

  /**
   * @return array
   */
  public function toSerializedArray()
  {
    return $this->copy()->map(function ($item) {
      return (is_object($item) && method_exists($item, 'toSerializedArray')) ?
          $item->toSerializedArray() :
          json_decode(json_encode($item), true); // Do anyone know a better way? :)
    })->toArray();
  }

  /**
   * @return string
   */
  public function toJson()
  {
    return json_encode($this->toSerializedArray());
  }

  /**
   * @return Collection
   */
  public function copy()
  {
    return clone $this;
  }

  /**
   * @return array
   */
  public function getKeys()
  {
    return array_keys($this->_data);
  }

  /**
   * @param int $key
   * @return bool
   */
  public function hasKey($key)
  {
    return array_key_exists((int) $key, $this->_data);
  }

  /**
   * @return bool
   */
  public function isEmpty()
  {
    return $this->count() === 0;
  }

  /**
   * @return array
   */
  public function getValues()
  {
    return array_values($this->_data);
  }

  /**
   * @param mixed $value
   * @return bool
   */
  public function hasValue($value)
  {
    return in_array($value, $this->_data);
  }

  /**
   * @return int
   */
  public function count()
  {
    return count($this->_data);
  }

  /**
   * @return $this
   */
  public function clear()
  {
    $this->_data = [];
    return $this;
  }

  /**
   * @return $this
   */
  private function _clearIndexes()
  {
    $this->_data = $this->getValues();
    return $this;
  }

  /**
   * @param int $key
   * @return mixed
   */
  public function get($key)
  {
    return $this->hasKey((int) $key) ? $this->_data[(int) $key] : null;
  }

  /**
   * @param int $key
   * @param bool $do_not_clear
   * @return Collection
   */
  public function remove($key, $do_not_clear = false)
  {
    if ($this->hasKey((int) $key)) {
      unset($this->_data[(int) $key]);
    }
    if ($do_not_clear) {
      return $this;
    }
    return $this->_clearIndexes();
  }

  /**
   * @param \Closure $call
   * @return $this
   */
  public function each(\Closure $call)
  {
    foreach ($this->_data as $key=>$item) {
      if ($call($item, $key) === false) {
        break;
      }
    }
    return $this;
  }

  /**
   * @param \Closure $call
   * @return $this
   */
  public function filter(\Closure $call)
  {
    $instance = $this;
    $this->each(function ($item, $key) use ($call, $instance) {
      $exit = $call($item, $key);
      if ($exit !== true) {
        $instance->remove($key, true);
      }
    });
    return $this->_clearIndexes();
  }

  /**
   * @return $this
   */
  public function reverse()
  {
    $this->_data = array_reverse($this->_data);
    return $this;
  }

  /**
   * @return mixed
   */
  public function first()
  {
    return $this->get(0);
  }

  /**
   * @return mixed
   */
  public function last()
  {
    return $this->get($this->count() - 1);
  }

  /**
   * @return mixed
   */
  public function pop()
  {
    return array_pop($this->_data);
  }

  /**
   * @param mixed $value
   * @return $this
   */
  public function push($value)
  {
    array_push($this->_data, $value);
    return $this;
  }

  /**
   * @param int $key
   * @param $value
   * @return $this
   */
  public function put($key, $value)
  {
    $this->_data[(int) $key] = $value;
    return $this;
  }

  /**
   * @return mixed
   */
  public function shift()
  {
    return array_shift($this->_data);
  }

  /**
   * @param mixed $value
   * @return $this
   */
  public function unshift($value)
  {
    array_unshift($this->_data, $value);
    if ($this->_pointer >= 0) {
      $this->_pointer++;
    }
    return $this;
  }

  /**
   * @param int $key
   * @param $data
   * @return $this
   */
  public function inject($key, $data)
  {
    array_splice($this->_data, (int) $key, 0, $data);
    if ($key <= $this->_pointer) {
      $this->_pointer++;
    }
    return $this;
  }

  /**
   * @param array|Collection $data
   * @return $this
   */
  public function merge($data)
  {
    if ($data instanceof Collection) {
      $this->_data = array_merge($this->_data, $data->toArray());
    } else {
      $this->_data = array_merge($this->_data, array_values($data));
    }
    $this->_clearIndexes();
    return $this;
  }

  /**
   * @param \Closure $call
   * @return $this
   */
  public function sort(\Closure $call)
  {
    usort($this->_data, $call);
    return $this;
  }

  /**
   * @param \Closure $call
   * @return Collection
   */
  public function map(\Closure $call)
  {
    $instance = $this;
    return $this->each(function ($item, $key) use ($call, $instance) {
      $instance->put($key, $call($item, $key));
    });
  }

  /**
   * @param $offset
   * @param $length
   * @return $this
   */
  public function slice($offset, $length = null)
  {
    $this->_data = array_slice($this->_data, $offset, $length);
    return $this;
  }

  /**
   * @param $size
   * @return Collection
   */
  public function chunk($size)
  {
    return array_map(function ($item) {
      return new Collection($item);
    }, array_chunk($this->_data, $size));
  }

  /**
   * @return mixed
   */
  public function current()
  {
    return $this->get($this->_pointer);
  }

  /**
   * @return mixed
   */
  public function next()
  {
    $this->_pointer++;
    return $this->current();
  }

  /**
   * @return mixed
   */
  public function previous()
  {
    $this->_pointer--;
    return $this->current();
  }

  /**
   * @param int $pointer
   * @return $this
   */
  public function setPointer($pointer)
  {
    $this->_pointer = (int) $pointer;
    return $this;
  }

  /**
   * @param string|string[] $keys
   * @return array
   */
  public function lists($keys)
  {
    $keys = (array) $keys;
    return $this->copy()->map(function ($item) use ($keys) {
      $data = [];
      foreach ($keys as $key) {
        if (is_object($item)) {
          $data[$key] = $item->{$key};
        } elseif (is_array($item)) {
          $data[$key] = $item[$key];
        }
      }
      return $data;
    })->toArray();
  }

  /**
   * @param string $glue
   * @return string
   */
  public function join($glue)
  {
    return implode($glue, $this->_data);
  }

}
