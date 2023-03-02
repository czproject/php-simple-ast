<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Reflection;

	use CzProject\PhpSimpleAst\Ast;
	use Nette\Utils\Strings;


	class SourceAliases
	{
		/** @var Aliases|NULL */
		private $aliases;

		/** @var array<string, Aliases> */
		private $namespaceAliases = [];



		public function getAliases(?Ast\NamespaceNode $namespaceNode): Aliases
		{
			if ($namespaceNode === NULL) { // source aliases
				if ($this->aliases === NULL) {
					$this->aliases = new Aliases(NULL);
				}

				return $this->aliases;

			}

			$nodeKey = spl_object_hash($namespaceNode) . '&' . $namespaceNode->getName();

			if (!isset($this->namespaceAliases[$nodeKey])) {
				$this->namespaceAliases[$nodeKey] = new Aliases($namespaceNode->getName());
			}

			return $this->namespaceAliases[$nodeKey];
		}
	}
