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


		public function __construct(string $indentation, string $literal)
		{
			Assert::true($literal !== '', 'Missing literal.');

			$this->indentation = $indentation;
			$this->literal = $literal;
		}


		public function getLiteral(): string
		{
			return $this->literal;
		}


		public function toString(): string
		{
			return $this->indentation . $this->literal;
		}


		public static function parseParenthesisExpression(NodeParser $parser): self
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
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
				$nodeIndentation,
				$literal
			);
		}


		public static function parseBlockExpression(NodeParser $parser): self
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
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
				$nodeIndentation,
				$literal
			);
		}


		/**
		 * @param array<int|string> $endingTokens
		 */
		public static function parseExpression(NodeParser $parser, array $endingTokens): self
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
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
				$nodeIndentation,
				$literal
			);
		}


		/**
		 * @param int|string|array<int|string> $tokenType
		 */
		public static function parseToken(NodeParser $parser, int|string|array $tokenType): self
		{
			$nodeIndentation = $parser->consumeNodeIndentation();

			if (is_array($tokenType)) {
				$literal = $parser->consumeTokenAsText(...$tokenType);

			} else {
				$literal = $parser->consumeTokenAsText($tokenType);
			}

			$parser->close();

			return new self(
				$nodeIndentation,
				$literal
			);
		}
	}
