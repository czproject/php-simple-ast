<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Reflection;

	use Nette\Utils\Strings;


	class Aliases
	{
		/** @var string|NULL */
		private $namespace;

		/** @var array<string, string> */
		private $classAliases = [];


		public function __construct(?string $namespace)
		{
			$this->namespace = $namespace;
		}


		public function getNamespace(): ?string
		{
			return $this->namespace;
		}


		public function addClassAlias(string $alias, string $name): void
		{
			$key = Strings::lower($alias);

			if (isset($this->classAliases[$key])) {
				throw new \CzProject\PhpSimpleAst\InvalidStateException("Alias '$alias' is already in use.");
			}

			$this->classAliases[$key] = $name;
		}


		public function translateClassName(string $name): string
		{
			$namespace = $this->namespace;

			if (Strings::startsWith($name, 'namespace\\')) {
				$name = Strings::substring($name, 10);

			} elseif (strpos($name, '\\')) {
				$key = Strings::before(Strings::lower($name), '\\');

				if (isset($this->classAliases[$key])) {
					$namespace = $this->classAliases[$key];
				}

			} elseif (isset($this->classAliases[$key = Strings::lower($name)])) {
				return $this->classAliases[$key];
			}

			return Reflection::translateName($name, $namespace);
		}
	}
