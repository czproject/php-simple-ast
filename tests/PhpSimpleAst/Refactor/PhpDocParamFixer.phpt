<?php

declare(strict_types=1);

use CzProject\PhpSimpleAst;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

$reflection = PhpSimpleAst\Reflection\FilesReflection::scanFile(Fixtures::path('Refactoring/PhpDocParamFixer.php'));


test('@param fixer', function () use ($reflection) {
	$classReflection = $reflection->getClass(\Foo\Bar::class);
	PhpSimpleAst\Refactor\PhpDocParamFixer::processClass($classReflection);

	$newContent = <<<DOCCOMMENT
/**
 * @param string \$name  description \$var
 * @param int \$age
 * @param string \$email
 * @param array<string, mixed> \$parameters
 * @param array<string, mixed> \$parameters2
 *
 * @return void
 */
DOCCOMMENT;
	Assert::same($newContent, $classReflection->getMethod('setName')->getDocComment());
});
