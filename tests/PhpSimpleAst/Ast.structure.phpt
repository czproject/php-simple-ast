<?php

declare(strict_types=1);

use CzProject\PhpSimpleAst\AstParser;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';


test('AST structure', function () {
	$astParser = new AstParser;

	foreach (Fixtures::getAll() as $entry) {
		echo $entry, "\n";
		$content = Fixtures::load($entry);
		$node = $astParser->parseString($content);

		Assert::same(
			Fixtures::load($entry, 'dump'),
			\Tracy\Dumper::toText($node, [
				\Tracy\Dumper::HASH => FALSE,
				\Tracy\Dumper::TRUNCATE => 1000,
			])
		);
	}
});
