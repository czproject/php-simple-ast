<?php

declare(strict_types=1);

use CzProject\PhpSimpleAst\Lexer\PhpToken;
use CzProject\PhpSimpleAst\Lexer\PhpTokens;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

test('Fixing of trailing newlines after T_COMMENT', function () {
	$tokens = PhpTokens::fromSource("<?php // commentA\r\n // commentB");

	Assert::equal(
		new PhpToken(T_OPEN_TAG, '<?php ', 0, 1),
		$tokens->next()
	);

	Assert::equal(
		new PhpToken(T_COMMENT, '// commentA', 1, 1),
		$tokens->next()
	);

	Assert::equal(
		new PhpToken(T_WHITESPACE, "\r\n ", 2, 1),
		$tokens->next()
	);

	Assert::equal(
		new PhpToken(T_COMMENT, '// commentB', 3, 2),
		$tokens->next()
	);

	Assert::false($tokens->hasToken());
});
