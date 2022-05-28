<?php

declare(strict_types=1);

use CzProject\PhpSimpleAst;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$reflection = PhpSimpleAst\Reflection\FilesReflection::scanFile(Fixtures::path('Reflection.php'));


test('Class list', function () use ($reflection) {
	$names = [];

	foreach ($reflection->getClasses() as $classReflection) {
		Assert::type(PhpSimpleAst\Reflection\ClassReflection::class, $classReflection);
		$names[] = $classReflection->getName();
	}

	Assert::same([
		MyClass::class,
	], $names);
});


test('Class methods', function () use ($reflection) {
	$names = [];

	foreach ($reflection->getClass(MyClass::class)->getMethods() as $methodReflection) {
		Assert::type(PhpSimpleAst\Reflection\MethodReflection::class, $methodReflection);
		$names[] = $methodReflection->getName();
	}

	Assert::same([
		'getName',
	], $names);
});
