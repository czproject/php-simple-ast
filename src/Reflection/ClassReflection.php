<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Reflection;

	use CzProject\PhpSimpleAst\Ast;


	class ClassReflection
	{
		/** @var string */
		private $name;

		/** @var array<string, MethodReflection> */
		private $methods = [];


		/**
		 * @param MethodReflection[] $methods
		 */
		private function __construct(
			string $name,
			array $methods
		)
		{
			$this->name = $name;

			foreach ($methods as $method) {
				$name = $method->getName();

				if (isset($this->methods[$name])) {
					throw new \CzProject\PhpSimpleAst\InvalidStateException('Duplicated method ' . $name);
				}

				$this->methods[$name] = $method;
			}
		}


		public function getName(): string
		{
			return $this->name;
		}


		/**
		 * @return array<string, MethodReflection>
		 */
		public function getMethods(): array
		{
			return $this->methods;
		}


		/**
		 * @param  Ast\PhpFile|Ast\PhpString $source
		 * @return self[]
		 */
		public static function createFromSource($source): array
		{
			$result = [];
			$classTrees = Ast\Tree::find($source, Ast\ClassNode::class);

			foreach ($classTrees as $classTree) {
				$classNode = $classTree->getNode();
				$namespaceNode = $classTree->closest(Ast\NamespaceNode::class);
				$namespaceName = $namespaceNode !== NULL ? $namespaceNode->getName() : NULL;
				$className = ($namespaceName !== NULL ? "$namespaceName\\" : '') . $classNode->getName();
				$result[] = new self($className, MethodReflection::createFromClass($className, $classNode));
			}

			return $result;
		}
	}
