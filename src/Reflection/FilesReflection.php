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


		private function getReflection(): Reflection
		{
			if ($this->reflection === NULL) {
				$this->reflection = new Reflection($this->files);
			}

			return $this->reflection;
		}


		/**
		 * @return self
		 */
		public static function scanFile(string $path)
		{
			$astParser = new AstParser;
			return new self([$astParser->parseFile($path)]);
		}


		/**
		 * @param  string|string[] $directories
		 * @return self
		 */
		public static function scanDirectories($directories)
		{
			$astParser = new AstParser;
			$files = [];
			$finder = \Nette\Utils\Finder::findFiles('*.php')
				->from($directories);

			foreach ($finder as $path => $file) {
				assert(is_string($path));

				if (isset($files[$path])) {
					continue;
				}

				$files[$path] = $astParser->parseFile($path);
			}

			return new self($files);
		}
	}
