<?php

declare(strict_types=1);

use CzProject\PhpSimpleAst;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


test('nullable parameter fixer', function () {
	$reflection = PhpSimpleAst\Reflection\FilesReflection::scanFile(Fixtures::path('Refactoring/NullableParameterFixer.php'));
	$classReflection = $reflection->getClass(\Foo\TestClass::class);

	PhpSimpleAst\Refactor\NullableParameterFixer::processClass($classReflection);

	$method = $classReflection->getMethod('methodWithNullableParams');
	$parameters = $method->getParameters();

	// param1: string $param1 = null should become nullable
	Assert::true($parameters['param1']->isNullable());

	// param2: int $param2 = 42 should NOT become nullable (default is not null)
	Assert::false($parameters['param2']->isNullable());

	// param3: array $param3 = null should become nullable
	Assert::true($parameters['param3']->isNullable());

	// param4: $param4 = null should NOT be affected (no type declaration)
	Assert::false($parameters['param4']->isNullable());

	// Test method without defaults - should not be affected
	$method2 = $classReflection->getMethod('methodWithoutDefaults');
	$parameters2 = $method2->getParameters();
	Assert::false($parameters2['param1']->isNullable());
	Assert::false($parameters2['param2']->isNullable());

	// Test method with already nullable types - should remain unchanged
	$method3 = $classReflection->getMethod('methodWithNullableTypes');
	$parameters3 = $method3->getParameters();
	Assert::true($parameters3['param1']->isNullable());
	Assert::true($parameters3['param2']->isNullable());

	Assert::same(
		Fixtures::load('Refactoring/NullableParameterFixer.result'),
		$reflection->getFiles()[0]->toString()
	);
});
