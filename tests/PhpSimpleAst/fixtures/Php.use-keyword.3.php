<?php
// conflicts
namespace First;
use NS4\NS5\NS6;
use NS4\NS5\NS7 as NS9;

class MyClass1 extends NS9\ParentClass implements NS6\FooInterface
{
}


namespace Second;
use MYNS\NS6;

class MyClass2 extends NS9\ParentClass implements NS6\FooInterface
{
}
