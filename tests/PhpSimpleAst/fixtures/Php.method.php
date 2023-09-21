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


		function paramWithCommentOnly(/*, $testMode*/)
		{
		}


		function paramWithCommentEverywhere(
			/*A1*/$login/*A2*/,
			/*B1*/array/*B2*/$args/*B3*/
		)
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
