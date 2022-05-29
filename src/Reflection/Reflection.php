<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Reflection;

	use CzProject\Assert\Assert;
	use CzProject\PhpSimpleAst\Ast;
	use Nette\Utils\Strings;


	class Reflection
	{
		/** @var Ast\IPhpSource[] */
		private $sources = [];

		/** @var array<string, ClassReflection>|NULL */
		private $classes = NULL;


		/**
		 * @param Ast\IPhpSource[] $sources
		 */
		public function __construct(array $sources)
		{
			$this->sources = $sources;
		}


		/**
		 * @return ClassReflection[]
		 */
		public function getClasses(): array
		{
			if ($this->classes === NULL) {
				$this->classes = [];

				foreach ($this->sources as $source) {
					foreach (ClassReflection::createFromSource($source) as $classReflection) {
						$className = strtolower($classReflection->getName());

						if (isset($this->classes[$className])) {
							throw new \CzProject\PhpSimpleAst\InvalidStateException('Duplicated class ' . $classReflection->getName());
						}

						$this->classes[$className] = $classReflection;
					}
				}
			}

			return array_values($this->classes);
		}


		public function getClass(string $className): ClassReflection
		{
			$this->getClasses();
			$key = strtolower($className);

			if (!isset($this->classes[$key])) {
				throw new \CzProject\PhpSimpleAst\InvalidStateException('Missing class ' . $className);
			}

			return $this->classes[$key];
		}


		/**
		 * @return ClassReflection[]
		 */
		public function getFamilyLine(string $className): array
		{
			$result = [];
			$parent = $this->getClass($className);
			$result[] = $parent;

			while ($parent->hasParent()) {
				$parent = $this->getClass($parent->getParentName());
				$result[] = $parent;
			}

			return $result;
		}


		/**
		 * @return array<string, MethodReflection>
		 */
		public function getMethods(string $className): array
		{
			$result = [];

			foreach (array_reverse($this->getFamilyLine($className)) as $parent) {
				foreach ($parent->getMethods() as $method) {
					$result[$method->getName()] = $method;
				}
			}

			return $result;
		}


		/**
		 * @return MethodReflection
		 */
		public function getMethod(string $className, string $methodName): MethodReflection
		{
			$parent = $this->getClass($className);

			if ($parent->hasMethod($methodName)) {
				return $parent->getMethod($methodName);
			}

			$methods = $this->getMethods($className);
			Assert::true(isset($methods[$methodName]), 'Method ' . $methodName . ' not found.');
			return $methods[$methodName];
		}


		public static function translateName(string $name, ?string $namespace): string
		{
			if (Strings::startsWith($name, '\\')) {
				return Strings::substring($name, 1);
			}

			return ($namespace !== NULL ? "$namespace\\" : '') . $name;
		}
	}
