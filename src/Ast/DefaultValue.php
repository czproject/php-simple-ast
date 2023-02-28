<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class DefaultValue implements INode
	{
		/** @var string */
		private $indentation;

		/** @var string */
		private $operator;

		/** @var Literal */
		private $literal;


		/**
		 * @param string $indentation
		 * @param string $operator
		 */
		public function __construct($indentation, $operator, Literal $literal)
		{
			Assert::string($indentation);
			Assert::string($operator);
			Assert::true($operator === '=', 'Invalid operator');

			$this->indentation = $indentation;
			$this->operator = $operator;
			$this->literal = $literal;
		}


		public function toString()
		{
			return $this->indentation . $this->operator . $this->literal->toString();
		}


		/**
		 * @return self
		 */
		public static function parseForFunctionParameter(NodeParser $parser)
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
			$operator = $parser->consumeTokenAsText('=');
			$parser->tryConsumeWhitespace();
			$literal = Literal::parseExpression($parser->createSubParser(), [',', ')']);

			$parser->close();

			return new self(
				$nodeIndentation,
				$operator,
				$literal
			);
		}


		/**
		 * @return self
		 */
		public static function parseForProperty(NodeParser $parser)
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
			$operator = $parser->consumeTokenAsText('=');
			$parser->tryConsumeWhitespace();
			$literal = Literal::parseExpression($parser->createSubParser(), [';']);

			$parser->close();

			return new self(
				$nodeIndentation,
				$operator,
				$literal
			);
		}
	}
