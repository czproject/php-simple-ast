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
		function setName($name, $age)
		{
		}
	}
}
