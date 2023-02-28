<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class ConstantNode implements INode
	{
		/** @var string */
		private $indentation;

		/** @var IConstantModifier[] */
		private $modifiers;

		/** @var Literal */
		private $keyword;

		/** @var Literal */
		private $name;

		/** @var DefaultValue */
		private $defaultValue;

		/** @var string */
		private $closer;


		/**
		 * @param IConstantModifier[] $modifiers
		 */
		public function __construct(
			string $indentation,
			array $modifiers,
			Literal $keyword,
			Literal $name,
			DefaultValue $defaultValue,
			string $closer
		)
		{
			$this->indentation = $indentation;
			$this->modifiers = $modifiers;
			$this->keyword = $keyword;
			$this->name = $name;
			$this->defaultValue = $defaultValue;
			$this->closer = $closer;
		}


		public function toString()
		{
			$s = $this->indentation;

			foreach ($this->modifiers as $modifier) {
				$s .= $modifier->toString();
			}

			$s .= $this->keyword->toString();
			$s .= $this->name->toString();
			$s .= $this->defaultValue->toString();
			$s .= $this->closer;
			return $s;
		}


		/**
		 * @return self
		 */
		public static function parse(Modifiers $modifiers, NodeParser $parser)
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
			$keyword = $parser->consumeTokenAsText(T_CONST);
			$parser->tryConsumeWhitespace();
			$name = Literal::parseToken($parser->createSubParser(), T_STRING);
			$parser->tryConsumeWhitespace();
			$defaultValue = DefaultValue::parseForProperty($parser->createSubParser());
			$closer = $parser->flushIndentation() . $parser->consumeTokenAsText(';');
			$parser->close();

			return new self(
				$modifiers->getIndentation(),
				$modifiers->toConstantModifiers(),
				new Literal($nodeIndentation, $keyword),
				$name,
				$defaultValue,
				$closer
			);
		}
	}
