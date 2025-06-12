<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class NamedType
	{
		/** @var string */
		private $indentation;

		/** @var string */
		private $nullableSign;

		/** @var string */
		private $typeIndentation;

		/** @var string */
		private $type;


		public function __construct(
			string $indentation,
			string $nullableSign,
			string $typeIndentation,
			string $type
		)
		{
			Assert::true($nullableSign === '' || $nullableSign === '?');

			$this->indentation = $indentation;
			$this->nullableSign = $nullableSign;
			$this->typeIndentation = $typeIndentation;
			$this->type = $type;
		}


		public function toString(): string
		{
			if ($this->nullableSign !== '') {
				return $this->indentation . $this->nullableSign . $this->typeIndentation . $this->type;
			}

			return $this->indentation . $this->type;
		}


		public function isNullable(): bool
		{
			return $this->nullableSign === '?';
		}


		public function setNullable(bool $nullable): void
		{
			$this->nullableSign = $nullable ? '?' : '';
		}


		public static function parse(NodeParser $parser): self
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
			$nullableSign = '';
			$typeIndentation = '';

			if ($parser->isCurrent('?')) {
				$nullableSign = $parser->consumeTokenAsText('?');
				$typeIndentation = $parser->tryConsumeAllTokensAsText(T_WHITESPACE);
			}

			if ($parser->isCurrent(T_ARRAY)) {
				$type = $parser->consumeTokenAsText(T_ARRAY);

			} elseif ($parser->isCurrent(T_CALLABLE)) {
				$type = $parser->consumeTokenAsText(T_CALLABLE);

			} elseif ($parser->isCurrent(T_STATIC)) {
				$type = $parser->consumeTokenAsText(T_STATIC);

			} else {
				$name = Name::parse($parser->createSubParser());
				$type = $name->toString();
			}

			$parser->close();
			return new self($nodeIndentation, $nullableSign, $typeIndentation, $type);
		}
	}
