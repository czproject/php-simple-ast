<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class UseNode implements IParentNode
	{
		/** @var string */
		private $indentation;

		/** @var Literal|NULL */
		private $type;

		/** @var string */
		private $keyword;

		/** @var array{UseGroupNode}|array<UseAliasNode> */
		private $children;

		/** @var string */
		private $closer;


		/**
		 * @param array{UseGroupNode}|array<UseAliasNode> $children
		 */
		public function __construct(
			string $indentation,
			string $keyword,
			Literal $type = NULL,
			array $children,
			string $closer
		)
		{
			Assert::true(count($children) > 0, 'Children cannot be empty.');
			$this->indentation = $indentation;
			$this->type = $type;
			$this->keyword = $keyword;
			$this->children = $children;
			$this->closer = $closer;
		}


		/**
		 * @return array{UseGroupNode}|array<UseAliasNode>
		 */
		public function getNodes()
		{
			return $this->children;
		}


		public function toString()
		{
			$s = $this->indentation . $this->keyword;

			if ($this->type !== NULL) {
				$s .= $this->type->toString();
			}

			foreach ($this->children as $child) {
				$s .= $child->toString();
			}

			return $s . $this->closer;
		}


		/**
		 * @return self
		 */
		public static function parse(NodeParser $parser)
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
			$keyword = $parser->consumeTokenAsText(T_USE);
			$parser->consumeWhitespace();

			$type = NULL;

			if ($parser->isCurrent(T_CONST, T_FUNCTION)) {
				$type = Literal::parseToken($parser->createSubParser(), [T_CONST, T_FUNCTION]);
				$parser->consumeWhitespace();
			}

			$name = Name::parse($parser->createSubParser());
			$parser->tryConsumeWhitespace();
			$children = [];

			if ($parser->isCurrent(T_NS_SEPARATOR, '{')) { // https://www.php.net/manual/en/language.namespaces.importing.php#language.namespaces.importing.group
				$child = UseGroupNode::parse($parser->createSubParser(), $name);
				$parser->onChild($child);
				$children[] = $child;

			} else {
				$child = UseAliasNode::parseAlias($parser->createSubParser(), $name);
				$parser->onChild($child);
				$children[] = $child;

				$parser->tryConsumeWhitespace();

				while ($parser->isCurrent(',')) {
					$parser->consumeAsIndentation(',');
					$parser->tryConsumeWhitespace();
					$child = UseAliasNode::parse($parser->createSubParser());
					$parser->onChild($child);
					$children[] = $child;

					$parser->tryConsumeWhitespace();
				}
			}

			$closer = $parser->consumeTokenAsText(';');

			$parser->close();

			return new self(
				$nodeIndentation,
				$keyword,
				$type,
				$children,
				$closer
			);
		}
	}
