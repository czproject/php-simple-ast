<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Reflection;

	use CzProject\PhpSimpleAst\Ast;


	class MethodReflection
	{
		/** @var string */
		private $declaringClassName;

		/** @var Ast\MethodNode */
		private $node;


		private function __construct(
			string $declaringClassName,
			Ast\MethodNode $node
		)
		{
			$this->declaringClassName = $declaringClassName;
			$this->node = $node;
		}


		public function getDeclaringClassName(): string
		{
			return $this->declaringClassName;
		}


		public function getName(): string
		{
			return $this->node->getName();
		}


		public function getFullName(): string
		{
			return $this->declaringClassName . '::' . $this->node->getName();
		}


		/**
		 * @return self[]
		 */
		public static function createFromClass(string $declaringClassName, Ast\ClassNode $classNode): array
		{
			$result = [];

			foreach (Ast\Tree::find($classNode, Ast\MethodNode::class) as $tree) {
				$result[] = new self($declaringClassName, $tree->getNode());
			}

			return $result;
		}
	}
