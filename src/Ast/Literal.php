<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class Literal
	{
		/** @var string */
		private $indentation;

		/** @var string */
		private $literal;


		/**
		 * @param string $indentation
		 * @param string $literal
		 */
		public function __construct($indentation, $literal)
		{
			Assert::string($indentation);
			Assert::string($literal);
			Assert::true($literal !== '', 'Missing literal.');

			$this->indentation = $indentation;
			$this->literal = $literal;
		}


		public function getLiteral(): string
		{
			return $this->literal;
		}


		/**
		 * @return string
		 */
		public function toString()
		{
			return $this->indentation . $this->literal;
		}


		/**
		 * @return self
		 */
		public static function parseParenthesisExpression(NodeParser $parser)
		{
			$literal = $parser->consumeTokenAsText('(');
			$level = 0;

			while ($parser->hasToken()) {
				if ($parser->isCurrent('(')) {
					$level++;
					$literal .= $parser->consumeTokenAsText('(');
					continue;

				} elseif ($parser->isCurrent(')')) {
					$level--;
					$literal .= $parser->consumeTokenAsText(')');

					if ($level <= 0) {
						break;
					}

				} else {
					$literal .= $parser->consumeAnythingAsText();
				}
			}

			$parser->close();

			return new self(
				$parser->getNodeIndentation(),
				$literal
			);
		}


		/**
		 * @return self
		 */
		public static function parseBlockExpression(NodeParser $parser)
		{
			$literal = $parser->consumeTokenAsText('{');
			$level = 0;

			while ($parser->hasToken()) {
				if ($parser->isCurrent('{')) {
					$level++;
					$literal .= $parser->consumeTokenAsText('{');
					continue;

				} elseif ($parser->isCurrent('}')) {
					$level--;
					$literal .= $parser->consumeTokenAsText('}');

					if ($level <= 0) {
						break;
					}

				} else {
					$literal .= $parser->consumeAnythingAsText();
				}
			}

			$parser->close();

			return new self(
				$parser->getNodeIndentation(),
				$literal
			);
		}


		/**
		 * @param  array<int|string> $endingTokens
		 * @return self
		 */
		public static function parseExpression(NodeParser $parser, array $endingTokens)
		{
			$level = 0;
			$literal = '';

			while ($parser->hasToken()) {
				if ($parser->isCurrent(...$endingTokens) && $level === 0) {
					break;

				} elseif ($parser->isCurrent(T_WHITESPACE)) {
					$parser->consumeWhitespace();

				} elseif ($parser->isCurrent('(')) {
					$level++;
					$literal .= $parser->flushIndentation() . $parser->consumeTokenAsText('(');
					continue;

				} elseif ($parser->isCurrent(')')) {
					$level--;
					$literal .= $parser->flushIndentation() . $parser->consumeTokenAsText(')');

					if ($level <= 0) {
						$level = 0;
					}

				} else {
					$literal .= $parser->flushIndentation() . $parser->consumeAnythingAsText();
				}
			}

			$parser->close();

			return new self(
				$parser->getNodeIndentation(),
				$literal
			);
		}


		/**
		 * @param  int|string $tokenType
		 * @return self
		 */
		public static function parseToken(NodeParser $parser, $tokenType)
		{
			$literal = $parser->consumeTokenAsText($tokenType);
			$parser->close();

			return new self(
				$parser->getNodeIndentation(),
				$literal
			);
		}
	}
