<?php

declare(strict_types=1);

use CzProject\PhpSimpleAst;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$reflection = PhpSimpleAst\Reflection\FilesReflection::scanFile(Fixtures::path('Reflection.aliases.php'));


test('Extends', function () use ($reflection) {
	$names = [];

	foreach ($reflection->getClasses() as $classReflection) {
		Assert::type(PhpSimpleAst\Reflection\ClassReflection::class, $classReflection);
		$names[$classReflection->getName()] = $classReflection->hasParent() ? $classReflection->getParentName() : NULL;
	}

	Assert::same([
		MyClass::class => People::class,
		MyPeople2::class => FooBar\People::class,
		Foo\Bar::class => MyClass::class,
		MyClass2::class => People::class,
		MyClass3::class => People::class,
		People::class => NULL,
		FooBar\People::class => NULL,
		FooBar\MyClass3::class => FooBar\People::class,
	], $names);
});
