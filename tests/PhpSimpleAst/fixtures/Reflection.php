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
