<?php namespace Dtkahl\ArrayTools;

class Map
{
  /**
   * @var array
   */
  private $_properties = [];

  public function __construct(array $properties = [])
  {
    $this->_properties = $properties;
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
    $this->_properties[(string) $key] = $value;
    return $this;
  }

  /**
   * @param $key
   * @return $this
   */
  public function remove($key)
  {
    if ($this->has((string) $key)) {
      unset($this->_properties[(string) $key]);
    }
    return $this;
  }

  /**
   * @return array
   */
  public function all()
  {
    return $this->_properties;
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
      $this->_properties = array_merge($this->_properties, $data->all());
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
   * @return array
   */
  public function toSerializedArray()
  {
    $array = [];
    foreach ($this->_properties as $key=>$property) {
      if (is_object($property) && method_exists($property, 'toSerializedArray')) {
        $array[$key] = $property->toSerializedArray();
      } else {
        $array[$key] = json_decode(json_encode($property), true); // Do anyone know a better way? :)
      }
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
   * @param array $keys
   * @return array
   */
  public function except(array $keys)
  {
    return array_diff_key($this->all(), array_flip($keys));
  }

  /**
   * @param array $keys
   * @return array
   */
  public function only(array $keys)
  {
    return array_intersect_key($this->all(), array_flip($keys));
  }

}
