<?php

// Basic class & interface definition
interface IMyInterface
{
}

class MyClass implements IMyInterface
{
}

use Foo\Bar;

interface IMyInterface extends Bar\FooBar
{
}

class MyClass extends Foo\Bar\Object implements IMyInterface
{
}
