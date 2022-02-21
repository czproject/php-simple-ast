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
					new Ast\ClassNode(
						"\n\n",
						'class',
						new Ast\Name(' ', 'MyClass'),
						NULL,
						NULL,
						' {',
						[],
						'}'
					),
					new Ast\ClassNode(
						"\n\n",
						'class',
						new Ast\Name(' ', 'MyClass2'),
						new Ast\ObjectExtends(' ', 'extends', new Ast\Name(' ', 'namespace\\MyParent')),
						new Ast\ObjectImplements(' ', 'implements', new Ast\Names(' ', [
							new Ast\Name('', 'namespace\\MyInterface'),
						])),
						' {',
						[],
						"\n\n}"
					),
					new Ast\UnknowNode("\n"),
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
					new Ast\ClassNode(
						"\n\t",
						'class',
						new Ast\Name(' ', 'MyClass'),
						NULL,
						new Ast\ObjectImplements(' ', 'implements', new Ast\Names(' ', [
							new Ast\Name('', 'MyInterface'),
						])),
						"\n\t{",
						[],
						"\n\t}"
					),
				],
				''
			),
			new Ast\NamespaceNode(
				"\n\n",
				'namespace',
				new Ast\Name(' ', 'NSecond'),
				';',
				[
					new Ast\ClassNode(
						"\n\t",
						'class',
						new Ast\Name(' ', 'MyClass2'),
						new Ast\ObjectExtends(' ', 'extends', new Ast\Name(' ', 'NThird\\ParentClass')),
						NULL,
						"\n\t{",
						[],
						"\n\t}"
					),
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
					),
					new Ast\ClassNode(
						"\n\n\t",
						'class',
						new Ast\Name(' ', 'MyClass3'),
						new Ast\ObjectExtends(' ', 'extends', new Ast\Name(' ', 'NS9\\ParentClass')),
						new Ast\ObjectImplements(' ', 'implements', new Ast\Names(' ', [
							new Ast\Name('', 'NS6\\FooInterface'),
						])),
						"\n\t{",
						[],
						"\n\t}"
					),
					new Ast\UnknowNode("\n"),
				],
				''
			),
		], NULL),
	]), $node);
});
