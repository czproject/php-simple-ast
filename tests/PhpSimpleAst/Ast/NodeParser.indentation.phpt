<?php

declare(strict_types=1);

use CzProject\PhpSimpleAst\Ast;
use CzProject\PhpSimpleAst\Lexer;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


test('Return indentation back', function () {
	$tokens = Lexer\PhpTokens::fromSource('<?php class {}');
	$stream = new Lexer\Stream($tokens);
	$parser = new Ast\NodeParser('', $stream);

	$parser->consumeNodeIndentation();
	$parser->consumeToken(T_OPEN_TAG);
	$parser->consumeToken(T_CLASS);
	$parser->consumeWhitespace();

	$subParser = $parser->createSubParser();

	$subParser2 = $parser->createSubParser();
	$subParser2->close();

	$subParser->close();

	Assert::same(' ', $parser->flushIndentation());
});
