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


test('Php.multi-ns.2', function () use ($astParser) {
	$node = $astParser->parseString(Fixtures::load('Php.multi-ns.2.php'));

	Assert::equal(new Ast\PhpString([
		new Ast\PhpNode("<?php\n", [
			new Ast\NamespaceNode(
				'',
				'namespace',
				new Ast\Name(' ', 'NFirst'),
				';',
				[
					new Ast\UnknowNode(
						"\n\tclass MyClass implements MyInterface"
						. "\n\t{"
						. "\n\t}"
					)
				],
				''
			),
			new Ast\NamespaceNode(
				"\n\n",
				'namespace',
				new Ast\Name(' ', 'NSecond'),
				';',
				[
					new Ast\UnknowNode(
						"\n\tclass MyClass2 extends NThird\ParentClass"
						. "\n\t{"
						. "\n\t}"
					)
				],
				''
			),
			new Ast\NamespaceNode(
				"\n\n",
				'namespace',
				new Ast\Name(' ', 'NThird'),
				';',
				[
					new Ast\UnknowNode(
						"\n\tuse NS4\NS5\NS6;"
						. "\n\tuse NS4\NS5\NS7 as NS9;"
						. "\n\tclass MyClass3 extends NS9\ParentClass implements NS6\FooInterface"
						. "\n\t{"
						. "\n\t}"
						. "\n"
					)
				],
				''
			),
		], NULL),
	]), $node);
});
