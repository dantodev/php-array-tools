<?php namespace Dtkahl\ArrayTools;

class Map
{
  /**
   * @var array
   */
  private $_properties  = [];
  private $_keys_locked = false;

  /**
   * @param $key
   * @return mixed
   */
  public function __get($key)
  {
    return $this->get($key);
  }

  /**
   * @param $key
   * @param $value
   */
  public function __set($key, $value)
  {
    $this->set($key, $value);
  }

  /**
   * @param $key
   * @return bool
   */
  public function __isset($key)
  {
    return $this->has($key);
  }

  /**
   * @param $key
   */
  public function __unset($key)
  {
    $this->remove($key);
  }

  /**
   * Map constructor.
   * @param array $properties
   * @param bool $keys_locked
   */
  public function __construct(array $properties = [], $keys_locked = false)
  {
    $this->_properties = $properties;
    $this->_keys_locked = $keys_locked;
  }

  /**
   * @param $key
   * @return bool
   */
  public function has($key)
  {
    return array_key_exists((string) $key, $this->_properties);
  }

  /**
   * @param $key
   * @param mixed $default
   * @return null
   */
  public function get($key, $default = null)
  {
    return $this->has((string) $key) ? $this->_properties[(string) $key] : $default;
  }

  /**
   * @param $key
   * @param mixed $value
   * @return $this
   */
  public function set($key, $value)
  {
    if (!$this->_keys_locked || $this->has($key)) {
      $this->_properties[(string) $key] = $value;
    } else {
      throw new \RuntimeException("Unknown map key '$key'.'");
    }
    return $this;
  }

  /**
   * @param $key
   * @return $this
   */
  public function remove($key)
  {
    if ($this->has((string) $key)) {
      if ($this->_keys_locked) {
        $this->set($key, null);
      } else {
        unset($this->_properties[(string) $key]);
      }
    }
    return $this;
  }

  /**
   * @param array $keys
   * @return Map
   */
  public function except(array $keys)
  {
    return new self(array_diff_key($this->toArray(), array_flip($keys)));
  }

  /**
   * @param array $keys
   * @return Map
   */
  public function only(array $keys)
  {
    return new self(array_intersect_key($this->toArray(), array_flip($keys)));
  }

  /**
   * @return array
   */
  public function toArray()
  {
    return $this->_properties;
  }

  /**
   * @return array
   */
  public function toSerializedArray()
  {
    $array = [];
    foreach ($this->toArray() as $key=>$item) {
      $array[$key] = (is_object($item) && method_exists($item, 'toSerializedArray')) ?
          $item->toSerializedArray() : json_decode(json_encode($item), true);  // Do anyone know a better way? :)
    }
    return $array;
  }

  /**
   * @return string
   */
  public function toJson()
  {
    return json_encode($this->toSerializedArray());
  }

  /**
   * @return array
   */
  public function getKeys()
  {
    return array_keys($this->_properties);
  }

  /**
   * @param $data
   * @return $this
   */
  public function merge($data)
  {
    if ($data instanceof self) {
      $this->_properties = array_merge($this->_properties, $data->toArray());
    } else {
      $this->_properties = array_merge($this->_properties, $data);
    }
    return $this;
  }

  /**
   * @return Map
   */
  public function copy()
  {
    return clone $this;
  }

  /**
   * @return $this
   */
  public function clear()
  {
    if ($this->_keys_locked) {
      $this->_properties = array_map(function () {return null;}, $this->_properties);
    } else {
      $this->_properties = [];
    }
    return $this;
  }

  /**
   * @return int
   */
  public function count()
  {
    return count($this->_properties);
  }

  /**
   * @return bool
   */
  public function isEmpty()
  {
  return $this->count() === 0;
  }

  /**
   * @param \Closure $call
   * @return $this
   */
  public function each(\Closure $call)
  {
    foreach ($this->_properties as $key=>$item) {
      if ($call($key, $item) === false) {
        break;
      }
    }
    return $this;
  }

}
