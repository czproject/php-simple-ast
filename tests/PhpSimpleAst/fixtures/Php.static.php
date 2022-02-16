<?php
class MyClass
{
	public function __construct()
	{
		$ret = self::fooBar();
		$set = static::fooBar();
		$get = parent::fooBar();
	}
}

use Foo\Bar;

class MyClass2
{
	public function __construct()
	{
		$ret = Bar::fooBar();
		$set = Foo::fooBar();
		$get = Bar\FooBar::fooBar();
	}
}
