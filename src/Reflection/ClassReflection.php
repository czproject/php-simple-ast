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

		/** @var string|NULL */
		private $fileName;


		/**
		 * @param MethodReflection[] $methods
		 */
		private function __construct(
			string $name,
			?string $parentName,
			array $methods,
			?string $fileName
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

			$this->fileName = $fileName;
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


		public function getFileName(): ?string
		{
			return $this->fileName;
		}


		/**
		 * @return self[]
		 */
		public static function createFromSource(Ast\IPhpSource $source): array
		{
			$result = [];
			$nodeTrees = Ast\Tree::find($source, [
				Ast\ClassNode::class,
				Ast\UseNode::class,
			]);
			$sourceAliases = new SourceAliases;
			$fileName = NULL;

			if ($source instanceof Ast\PhpFile) {
				$fileName = $source->getPath();
			}

			foreach ($nodeTrees as $nodeTree) {
				$node = $nodeTree->getNode();
				$namespaceNode = $nodeTree->closest(Ast\NamespaceNode::class);
				$aliases = $sourceAliases->getAliases($namespaceNode);

				if ($node instanceof Ast\ClassNode) {
					$namespaceName = $namespaceNode !== NULL ? $namespaceNode->getName() : NULL;
					$className = Reflection::translateName($node->getName(), $namespaceName);

				} elseif ($node instanceof Ast\UseNode) {
					if ($node->isForClasses()) {
						foreach ($node->getAliases() as $alias => $name) {
							$aliases->addClassAlias($alias, $name);
						}
					}

					continue;
				}

				$classNode = $node;
				$parentName = NULL;

				if ($classNode->hasExtends()) {
					$parentName = $aliases->translateClassName($classNode->getExtends()->getName());
				}

				$result[] = new self(
					$className,
					$parentName,
					MethodReflection::createFromClass($className, $classNode),
					$fileName
				);
			}

			return $result;
		}
	}
