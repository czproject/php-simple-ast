<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;


	class UseAliasNode implements INode
	{
		/** @var string */
		private $indentation;

		/** @var Name */
		private $name;

		/** @var Literal|NULL */
		private $alias;


		/**
		 * @param string $indentation
		 */
		public function __construct(
			string $indentation,
			Name $name,
			Literal $alias = NULL
		)
		{
			$this->indentation = $indentation;
			$this->name = $name;
			$this->alias = $alias;
		}


		/**
		 * @return string
		 */
		public function toString()
		{
			$s = $this->indentation . $this->name->toString();

			if ($this->alias !== NULL) {
				$s .= $this->alias->toString();
			}

			return $s;
		}


		/**
		 * @return self
		 */
		public static function parse(NodeParser $parser)
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
			$name = Name::parse($parser->createSubParser());
			$parser->tryConsumeWhitespace();
			$alias = NULL;

			if ($parser->isCurrent(T_AS)) {
				$parser->consumeAsIndentation(T_AS);
				$parser->consumeWhitespace();
				$alias = Literal::parseToken($parser->createSubParser(), T_STRING);
			}

			$parser->close();

			return new self(
				$nodeIndentation,
				$name,
				$alias
			);
		}


		/**
		 * @return self
		 */
		public static function parseAlias(NodeParser $parser, Name $name)
		{
			$alias = NULL;

			if ($parser->isCurrent(T_AS)) {
				$parser->consumeAsIndentation(T_AS);
				$parser->consumeWhitespace();
				$alias = Literal::parseToken($parser->createSubParser(), T_STRING);
			}

			$parser->close();

			return new self(
				$name->getIndentation(),
				new Name('', $name->getName()),
				$alias
			);
		}
	}
