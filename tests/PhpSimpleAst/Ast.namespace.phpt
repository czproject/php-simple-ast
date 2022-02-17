<?php

use CzProject\PhpSimpleAst\Ast;
use CzProject\PhpSimpleAst\AstParser;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$astParser = new AstParser;

test('Php.class-namespace', function () use ($astParser) {
	$node = $astParser->parseString(Fixtures::load('Php.class-namespace.php'));

	Assert::equal(new Ast\PhpString([
		new Ast\PhpNode("<?php\n", [
			new Ast\NamespaceNode(
				"\n",
				'namespace',
				new Ast\Name(' ', 'Foo\\Bar'),
				';',
				[
					new Ast\UnknowNode(
						"\n\nclass MyClass {}"
						. "\n\nclass MyClass2 extends namespace\MyParent implements namespace\MyInterface {"
						. "\n\n}\n"
					)
				],
				''
			),
		], NULL),
	]), $node);
});
