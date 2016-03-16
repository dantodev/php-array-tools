[![Latest Stable Version](https://poser.pugx.org/dtkahl/php-property-holder/v/stable)](https://packagist.org/packages/dtkahl/php-property-holder)
[![License](https://poser.pugx.org/dtkahl/php-property-holder/license)](https://packagist.org/packages/dtkahl/php-property-holder)

# PHP property holder

A property holder for PHP.


## Dependencies

* `PHP >= 7.0.0`


## Installation

Install with [Composer](http://getcomposer.org):
```
composer require dtkahl/php-property-holder
```


## Usage Example

```php
use Dtkahl\PropertyHolder\PropertyHolder;

class Example
{

  public $properties;
  
  public function __construct()
  {
    $this->properties = new PropertyHolder(['foo' => 'bar');
  }
  
  public function foo()
  {
    return $this->properties->get('foo');
  }

}
```


## Methods

#### `get(string $key, $default = null)`
Returns property value by given key or returns `$default` if property does not exist.

#### `has(string $key)`
Determine if an property with given key exists in the collection.

#### `set(string $key, $value)`
Set property (override if existing). Returns collection instance.

#### `remove(string $key)`
Remove property if existing. Returns collection instance.

#### `all()`
Returns all properties as array.
