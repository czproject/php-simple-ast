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


		public function __construct(string $indentation, string $operator, Literal $literal)
		{
			Assert::true($operator === '=', 'Invalid operator');

			$this->indentation = $indentation;
			$this->operator = $operator;
			$this->literal = $literal;
		}


		public function toString(): string
		{
			return $this->indentation . $this->operator . $this->literal->toString();
		}


		public static function parseForFunctionParameter(NodeParser $parser): self
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


		public static function parseForProperty(NodeParser $parser): self
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
