<?php
	class Greeting
	{
		public function say(
			namespace\Foo $namespacedFoo,
			Person $person,
			array $arr,
			callable $callable,
			object $obj,
			int $int,
			string $str,
			float $float,
			bool $bool,
			self $self,
			int & ... $numbers
		)
		{
		}

		function withComment() // comment
		{
		}


		function paramWithComment($login, $password/*, $testMode*/)
		{
		}


		function paramWithReference($directory, array &$result)
		{
		}
	}


	abstract class AbstractClass
	{
		abstract function abstractWithComment() /* asdf */;
	}


	class Person
	{
	}
