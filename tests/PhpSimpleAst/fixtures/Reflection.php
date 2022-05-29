<?php

namespace {
	class MyClass
	{
		function getName()
		{
		}
	}
}


namespace Foo {
	class Bar extends \MyClass
	{
		/**
		 * @param string
		 * @return void
		 */
		function setName($name, &$age)
		{
		}
	}
}
