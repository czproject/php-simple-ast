<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class UseGroupNode implements IParentNode
	{
		/** @var Name */
		private $prefix;

		/** @var string */
		private $opener;

		/** @var UseAliasNode[] */
		private $children;

		/** @var string */
		private $closer;


		/**
		 * @param UseAliasNode[] $children
		 */
		public function __construct(
			Name $prefix,
			string $opener,
			array $children,
			string $closer
		)
		{
			Assert::true(count($children) > 0, 'Children cannot be empty.');

			$this->prefix = $prefix;
			$this->opener = $opener;
			$this->children = $children;
			$this->closer = $closer;
		}


		/**
		 * @return array<string, string>
		 */
		public function getAliases(): array
		{
			$aliases = [];

			foreach ($this->children as $child) {
				$aliases[$child->getAlias()] = ltrim($this->prefix->getName() . '\\' . $child->getName(), '\\');
			}

			return $aliases;
		}


		/**
		 * @return UseAliasNode[]
		 */
		public function getNodes()
		{
			return $this->children;
		}


		public function toString(): string
		{
			$s = $this->prefix->toString();
			$s .= $this->opener;

			foreach ($this->children as $child) {
				$s .= $child->toString();
			}

			$s .= $this->closer;

			return $s;
		}


		public static function parse(NodeParser $parser, Name $prefix): self
		{
			$opener = $parser->consumeNodeIndentation();

			if ($parser->isCurrent(T_NS_SEPARATOR)) {
				$opener .= $parser->consumeTokenAsText(T_NS_SEPARATOR);
				$parser->tryConsumeWhitespace();
				$opener .= $parser->flushIndentation();
			}

			$opener .= $parser->consumeTokenAsText('{');
			$closer = '';

			$parser->tryConsumeWhitespace();
			$children = [];

			do {
				$child = UseAliasNode::parse($parser->createSubParser());
				$parser->onChild($child);
				$children[] = $child;

				$parser->tryConsumeWhitespace();

				if ($parser->isCurrent('}')) {
					$closer = $parser->flushIndentation() . $parser->consumeTokenAsText('}');
					break;
				}

				$parser->consumeAsIndentation(',');
				$parser->tryConsumeWhitespace();

			} while ($parser->hasToken());

			$parser->close();

			return new self(
				$prefix,
				$opener,
				$children,
				$closer
			);
		}
	}
