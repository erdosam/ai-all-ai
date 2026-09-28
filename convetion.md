# General convention

The `private` modifier for attributes and functions of a class is preferred than `protected`.
Attributes should be placed before functions of the class.
The order of the class member is `public`, `protected`, then `private`.
Constructor if exists, should be the first function in the order.

E.g. in PHP
```php
class MyClass
{
    public $attribute1;
    protected $attribute2;
    private $attribute3;
    public function __construct() {}
    public function doOne() {}
    protected function doTwo() {}
    private function doThree() {}
}
```
Basically uncle bob's clean code rule should be applied.