<?php
namespace NS1
{
	class MyClass implements MyInterface
	{
	}
}

namespace NS2
{
	class MyClass2 extends NS3\ParentClass
	{
	}
}

namespace NS3
{
	use NS4\NS5\NS6;
	use NS4\NS5\NS7 as NS9;

	class MyClass3 extends NS9\ParentClass implements NS6\FooInterface
	{
	}
}

namespace # global namespace
{
	class MyGlobalClass extends NS1\NS3\ParentClass2
	{
	}
}
