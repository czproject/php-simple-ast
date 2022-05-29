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
		Foo\Bar::class,
	], $names);
});


test('Family line', function () use ($reflection) {
	$names = [];

	foreach ($reflection->getFamilyLine(\Foo\Bar::class) as $classReflection) {
		Assert::type(PhpSimpleAst\Reflection\ClassReflection::class, $classReflection);
		$names[] = $classReflection->getName();
	}

	Assert::same([
		\Foo\Bar::class,
		\MyClass::class,
	], $names);
});


test('Class methods', function () use ($reflection) {
	$names = [];

	foreach ($reflection->getClass(MyClass::class)->getMethods() as $methodReflection) {
		Assert::type(PhpSimpleAst\Reflection\MethodReflection::class, $methodReflection);
		$names[] = $methodReflection->getFullName();
	}

	Assert::same([
		MyClass::class . '::getName',
	], $names);
});


test('Class inherited methods', function () use ($reflection) {
	$names = [];

	foreach ($reflection->getMethods(\Foo\Bar::class) as $methodReflection) {
		Assert::type(PhpSimpleAst\Reflection\MethodReflection::class, $methodReflection);
		$names[] = $methodReflection->getFullName();
	}

	Assert::same([
		MyClass::class . '::getName',
		Foo\Bar::class . '::setName',
	], $names);
});


test('Method parameters', function () use ($reflection) {
	$methodReflection = $reflection->getMethod(\Foo\Bar::class, 'setName');
	$names = [];

	foreach ($methodReflection->getParameters() as $parameterReflection) {
		Assert::type(PhpSimpleAst\Reflection\ParameterReflection::class, $parameterReflection);
		$names[$parameterReflection->getIndex()] = $parameterReflection->getName();
	}

	Assert::same([
		0 => 'name',
		1 => 'age',
	], $names);

	Assert::false($methodReflection->getParameterByIndex(0)->isPassedByReference());
	Assert::true($methodReflection->getParameterByIndex(1)->isPassedByReference());
});


test('Method PHPDoc', function () use ($reflection) {
	$methodReflection = $reflection->getMethod(\Foo\Bar::class, 'getName');
	Assert::null($methodReflection->getDocComment());

	$methodReflection = $reflection->getMethod(\Foo\Bar::class, 'setName');
	$content = <<<DOCCOMMENT
/**
 * @param string
 * @return void
 */
DOCCOMMENT;
	Assert::same($content, $methodReflection->getDocComment());
});
