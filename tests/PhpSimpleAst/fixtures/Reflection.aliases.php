<?php

namespace {
	class MyClass extends People // => \People
	{
		function getName()
		{
		}
	}

	use FooBar\People;

	class MyPeople2 extends People // => \FooBar\People
	{
	}
}


namespace Foo {
	class Bar extends \MyClass // => \MyClass
	{
		/**
		 * @param string
		 * @param int
		 * @param invalid
		 *
		 * @return void
		 */
		function setName($name, &$age)
		{
		}
	}
}


namespace {
	class MyClass2 extends People // => \People
	{
	}

	class MyClass3 extends namespace\People // => \People
	{
	}

	class People {}
}


namespace FooBar {
	class People {}

	class MyClass3 extends namespace\People // => FooBar\People
	{
	}
}
