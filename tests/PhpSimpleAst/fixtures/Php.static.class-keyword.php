<?php
// Basic class dependencies
namespace MyName\Space;

class MyClass
{
	public function __construct()
	{
		$ret = \Bar::class;
		$set = \Foo::class;
		$get = \Bar\FooBar::class;
		$let = self::class;
		$met = static::class;
	}
}

echo MyClass::class;
