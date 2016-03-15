<?php namespace Dtkahl\PropertyHolder;

class PropertyHolder
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
   * @param string $key
   * @return bool
   */
  public function has(string $key)
  {
    return array_key_exists($key, $this->_properties);
  }

  /**
   * @param string $key
   * @param mixed $default
   * @return null
   */
  public function get(string $key, $default = null)
  {
    return $this->has($key) ? $this->_properties[$key] : $default;
  }

  /**
   * @param string $key
   * @param mixed $value
   * @return $this
   */
  public function set(string $key, $value)
  {
    $this->_properties[$key] = $value;
    return $this;
  }

  /**
   * @param string $key
   * @return $this
   */
  public function remove(string $key)
  {
    if ($this->has($key)) {
      unset($this->_properties[$key]);
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

}
