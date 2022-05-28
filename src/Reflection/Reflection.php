<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Reflection;

	use CzProject\PhpSimpleAst\Ast;


	class Reflection
	{
		/** @var array<Ast\PhpFile|Ast\PhpString> */
		private $sources = [];

		/** @var array<string, ClassReflection>|NULL */
		private $classes = NULL;


		/**
		 * @param array<Ast\PhpFile|Ast\PhpString> $sources
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
	}
