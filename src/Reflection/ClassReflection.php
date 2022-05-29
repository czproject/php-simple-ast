<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Reflection;

	use CzProject\Assert\Assert;
	use CzProject\PhpSimpleAst\Ast;
	use CzProject\PhpSimpleAst\InvalidStateException;


	class ClassReflection
	{
		/** @var string */
		private $name;

		/** @var string|NULL */
		private $parentName;

		/** @var array<string, MethodReflection> */
		private $methods = [];


		/**
		 * @param MethodReflection[] $methods
		 */
		private function __construct(
			string $name,
			?string $parentName,
			array $methods
		)
		{
			$this->name = $name;
			$this->parentName = $parentName;

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


		public function hasParent(): bool
		{
			return $this->parentName !== NULL;
		}


		public function getParentName(): string
		{
			if ($this->parentName === NULL) {
				throw new InvalidStateException('Class has no parent.');
			}

			return $this->parentName;
		}


		/**
		 * @return array<string, MethodReflection>
		 */
		public function getMethods(): array
		{
			return $this->methods;
		}


		public function hasMethod(string $methodName): bool
		{
			return isset($this->methods[$methodName]);
		}


		public function getMethod(string $methodName): MethodReflection
		{
			Assert::true(isset($this->methods[$methodName]), 'Method ' . $methodName . ' not found.');
			return $this->methods[$methodName];
		}


		/**
		 * @return self[]
		 */
		public static function createFromSource(Ast\IPhpSource $source): array
		{
			$result = [];
			$classTrees = Ast\Tree::find($source, Ast\ClassNode::class);

			foreach ($classTrees as $classTree) {
				$classNode = $classTree->getNode();
				$namespaceNode = $classTree->closest(Ast\NamespaceNode::class);
				$namespaceName = $namespaceNode !== NULL ? $namespaceNode->getName() : NULL;
				$className = Reflection::translateName($classNode->getName(), $namespaceName);
				$parentName = NULL;

				if ($classNode->hasExtends()) {
					$parentName = Reflection::translateName($classNode->getExtends()->getName(), $namespaceName);
				}

				$result[] = new self(
					$className,
					$parentName,
					MethodReflection::createFromClass($className, $classNode)
				);
			}

			return $result;
		}
	}
