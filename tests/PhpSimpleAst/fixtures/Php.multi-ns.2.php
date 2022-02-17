<?php
namespace NFirst;
	class MyClass implements MyInterface
	{
	}

namespace NSecond;
	class MyClass2 extends NThird\ParentClass
	{
	}

namespace NThird;
	use NS4\NS5\NS6;
	use NS4\NS5\NS7 as NS9;

	class MyClass3 extends NS9\ParentClass implements NS6\FooInterface
	{
	}
