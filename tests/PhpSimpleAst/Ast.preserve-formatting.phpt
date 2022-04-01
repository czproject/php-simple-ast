<?php

declare(strict_types=1);

use CzProject\PhpSimpleAst\AstParser;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';


test('Preserve formatting', function () {
	$astParser = new AstParser;

	foreach (Fixtures::getAll() as $entry) {
		$content = Fixtures::load($entry);
		$node = $astParser->parseString($content);
		Assert::same($content, $node->toString(), 'Fixture: ' . $entry);
	}
});
