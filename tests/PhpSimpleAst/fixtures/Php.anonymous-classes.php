<?php

// http://php.net/manual/en/language.oop5.anonymous.php
// Pre PHP 7 code
class Logger
{
	public function log($msg)
	{
		echo $msg;
	}
}

$util->setLogger(new Logger());

// PHP 7+ code
$util->setLogger(new class {
	public function log($msg)
	{
		echo $msg;
	}
});


// example 2
class SomeClass {}
interface SomeInterface {}
trait SomeTrait {}

var_dump(new class(10) extends SomeClass implements SomeInterface {
	private $num;

	public function __construct($num)
	{
		$this->num = $num;
	}

	use SomeTrait;
});


// example 3
class Outer
{
	private $prop = 1;
	protected $prop2 = 2;

	protected function func1()
	{
		return 3;
	}

	public function func2()
	{
		return new class($this->prop) extends Outer {
			private $prop3;

			public function __construct($prop)
			{
				$this->prop3 = $prop;
			}

			public function func3()
			{
				return $this->prop2 + $this->prop3 + $this->func1();
			}
		};
	}
}

echo (new Outer)->func2()->func3();


// example 4
function anonymous_class()
{
	return new class {};
}

if (get_class(anonymous_class()) === get_class(anonymous_class())) {
	echo 'same class';
} else {
	echo 'different class';
}

// example 5
echo get_class(new class {});
