<?php namespace Dtkahl\ArrayTools;

class Map implements \ArrayAccess, \Countable, \Serializable
{
    /**
     * @var array
     */
    private $_properties = [];
    private $_options = [];

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
     * @param array $options
     */
    public function __construct(array $properties = [], array $options = [])
    {
        $this->_options = $options;
        if ($this->getOption('recursive', false)) {
            foreach ($properties as $key => $value) {
                if (is_array($value)) {
                    if (array_keys($value) === range(0, count($value) - 1)) {
                        $value = new Collection($value, $options);
                    } else {
                        $value = new self($value, $options);
                    }
                }
                $this->_properties[$key] = $value;
            }
        } else {
            $this->_properties = $properties;
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setOption($key, $value)
    {
        $this->_options[$key] = $value;
        return $this;
    }

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed|null
     */
    public function getOption($key, $default = null)
    {
        return array_key_exists($key, $this->_options) ? $this->_options[$key] : $default;
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists((string)$key, $this->_properties);
    }

    /**
     * @param array $keys
     * @return bool
     */
    public function hasKeys(array $keys)
    {
        return count(array_diff($keys, $this->getKeys())) === 0;
    }

    /**
     * @param $key
     * @param mixed $default
     * @return null
     */
    public function get($key, $default = null)
    {
        return $this->has((string)$key) ? $this->_properties[(string)$key] : $default;
    }

    /**
     * @param $key
     * @param mixed $value
     * @return $this
     */
    public function set($key, $value)
    {
        if (!$this->getOption('keys_locked', false) || $this->has($key)) {
            $this->_properties[(string)$key] = $value;
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
        if ($this->has((string)$key)) {
            if ($this->getOption('keys_locked')) {
                $this->set($key, null);
            } else {
                unset($this->_properties[(string)$key]);
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
        foreach ($this->toArray() as $key => $item) {
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
        if ($this->getOption('keys_locked')) {
            $this->_properties = array_map(function () {
                return null;
            }, $this->_properties);
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
        foreach ($this->_properties as $key => $item) {
            if ($call($key, $item) === false) {
                break;
            }
        }
        return $this;
    }

    /**
     * @param \Closure $call
     * @return Map
     */
    public function map(\Closure $call)
    {
        return $this->each(function ($key, $item) use ($call) {
            $this->set($key, $call($key, $item));
        });
    }

    /**
     * @param string $key
     * @return string
     */
    public function getType($key)
    {
        return gettype($this->get($key));
    }

    /**
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value) {
        if (!is_null($offset)) {
            $this->set($offset, $value);
        }
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return $this->has($offset);
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset) {
        $this->remove($offset);
    }

    /**
     * @param string $offset
     * @return null
     */
    public function offsetGet($offset) {
        return $this->get($offset);
    }

    public function serialize()
    {
        return serialize($this->_properties);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $this->_properties = unserialize($serialized);
    }

}
