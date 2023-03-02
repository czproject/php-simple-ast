<?php

declare(strict_types=1);

use CzProject\PhpSimpleAst;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$reflection = PhpSimpleAst\Reflection\FilesReflection::scanFile(Fixtures::path('Reflection.aliases.2.php'));


test('Extends', function () use ($reflection) {
	Assert::same(
		\App\Base\Presenters\BasePresenter::class,
		$reflection->getClass(\App\Front\Presenters\MyPresenter::class)->getParentName()
	);

	Assert::same(
		\App\Calc\CalculatorPresenter::class,
		$reflection->getClass(\App\Front\Presenters\FooPresenter::class)->getParentName()
	);

	Assert::same(
		\App\Calc\CalculatorPresenter::class,
		$reflection->getClass(\App\Front\Presenters\BarPresenter::class)->getParentName()
	);
});
