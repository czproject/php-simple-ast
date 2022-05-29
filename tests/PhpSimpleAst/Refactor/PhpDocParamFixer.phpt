<?php

declare(strict_types=1);

use CzProject\PhpSimpleAst;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

$reflection = PhpSimpleAst\Reflection\FilesReflection::scanFile(Fixtures::path('Reflection.php'));


test('@param fixer', function () use ($reflection) {
	$classReflection = $reflection->getClass(\Foo\Bar::class);
	PhpSimpleAst\Refactor\PhpDocParamFixer::processClass($classReflection);

	$newContent = <<<DOCCOMMENT
/**
 * @param string \$name
 * @param int \$age
 *
 * @return void
 */
DOCCOMMENT;
	Assert::same($newContent, $classReflection->getMethod('setName')->getDocComment());
});
