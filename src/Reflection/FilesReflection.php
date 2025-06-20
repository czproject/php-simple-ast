<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Reflection;

	use CzProject\PhpSimpleAst\Ast;
	use CzProject\PhpSimpleAst\AstParser;


	class FilesReflection
	{
		/** @var array<string, Ast\PhpFile> */
		private $files = [];

		/** @var Reflection|NULL */
		private $reflection = NULL;


		/**
		 * @param Ast\PhpFile[] $files
		 */
		public function __construct(array $files)
		{
			foreach ($files as $file) {
				if (isset($this->files[$file->getPath()])) {
					throw new \CzProject\PhpSimpleAst\InvalidStateException('File ' . $file->getPath() . ' already exists in reflection.');
				}

				$this->files[$file->getPath()] = $file;
			}
		}


		/**
		 * @return Ast\PhpFile[]
		 */
		public function getFiles(): array
		{
			return array_values($this->files);
		}


		/**
		 * @return ClassReflection[]
		 */
		public function getClasses(): array
		{
			return $this->getReflection()->getClasses();
		}


		public function getClass(string $className): ClassReflection
		{
			return $this->getReflection()->getClass($className);
		}


		/**
		 * @return ClassReflection[]
		 */
		public function getFamilyLine(string $className): array
		{
			return $this->getReflection()->getFamilyLine($className);
		}


		/**
		 * @return array<string, MethodReflection>
		 */
		public function getMethods(string $className): array
		{
			return $this->getReflection()->getMethods($className);
		}


		public function getMethod(string $className, string $methodName): MethodReflection
		{
			return $this->getReflection()->getMethod($className, $methodName);
		}


		public function isSubclassOf(string|ClassReflection $class, string $requiredClass): bool
		{
			return $this->getReflection()->isSubclassOf($class, $requiredClass);
		}


		private function getReflection(): Reflection
		{
			if ($this->reflection === NULL) {
				$this->reflection = new Reflection($this->files);
			}

			return $this->reflection;
		}


		public static function scanFile(string $path): self
		{
			$astParser = new AstParser;
			return new self([$astParser->parseFile($path)]);
		}


		/**
		 * @param  string|string[] $directories
		 */
		public static function scanDirectories(string|array $directories, ?callable $progress = NULL): self
		{
			if (is_string($directories)) {
				$directories = [$directories];
			}

			$astParser = new AstParser;
			$files = [];
			$finder = \Nette\Utils\Finder::findFiles('*.php')
				->from(...$directories);

			foreach ($finder as $path => $file) {
				if (isset($files[$path])) {
					continue;
				}

				if ($progress !== NULL) {
					$progress($path);
				}

				$files[$path] = $astParser->parseFile($path);
			}

			return new self($files);
		}
	}
