<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Reflection;

	use CzProject\PhpSimpleAst\Ast;


	class MethodReflection
	{
		/** @var Ast\MethodNode */
		private $node;


		public function __construct(
			Ast\MethodNode $node
		)
		{
			$this->node = $node;
		}


		public function getName(): string
		{
			return $this->node->getName();
		}


		/**
		 * @return self[]
		 */
		public static function createFromClass(Ast\ClassNode $classNode): array
		{
			$result = [];

			foreach (Ast\Tree::find($classNode, Ast\MethodNode::class) as $tree) {
				$result[] = new self($tree->getNode());
			}

			return $result;
		}
	}
