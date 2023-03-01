<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;


	/**
	 * @phpstan-template TNode of INode
	 */
	class Tree
	{
		/**
		 * @var INode
		 * @phpstan-var TNode
		 */
		private $node;

		/** @var IParentNode[] */
		private $parents;


		/**
		 * @phpstan-param TNode $node
		 * @param IParentNode[] $parents
		 */
		private function __construct(
			INode $node,
			array $parents
		)
		{
			$this->node = $node;
			$this->parents = array_reverse($parents);
		}


		/**
		 * @phpstan-return TNode
		 */
		public function getNode(): INode
		{
			return $this->node;
		}


		/**
		 * @phpstan-template TSearch of INode|IParentNode
		 * @param  string $nodeType
		 * @phpstan-param class-string<TSearch> $nodeType
		 * @return INode|IParentNode|NULL
		 * @phpstan-return TSearch|NULL
		 */
		public function closest($nodeType)
		{
			if ($this->node instanceof $nodeType) {
				return $this->node;
			}

			foreach ($this->parents as $parent) {
				if ($parent instanceof $nodeType) {
					return $parent;
				}
			}

			return NULL;
		}


		/**
		 * @phpstan-template TSearch of INode
		 * @param  string|string[] $nodeType
		 * @phpstan-param class-string<TSearch>|array<class-string<TSearch>> $nodeType
		 * @return self[]
		 * @phpstan-return self<TSearch>[]
		 */
		public static function find(IParentNode $root, $nodeType): array
		{
			$result = [];
			$stack = [];
			$stack[] = [$root, []];

			while (($parent = array_shift($stack)) !== NULL) {
				$parents = $parent[1];
				$parents[] = $parent[0];

				foreach ($parent[0]->getNodes() as $node) {
					if (is_array($nodeType)) {
						foreach ($nodeType as $nType) {
							if ($node instanceof $nType) {
								$result[] = new self($node, $parents);
							}
						}

					} elseif ($node instanceof $nodeType) {
						$result[] = new self($node, $parents);
					}

					if ($node instanceof IParentNode) {
						$stack[] = [$node, $parents];
					}
				}
			}

			return $result;
		}
	}
