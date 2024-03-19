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
			Assert::string($indentation);
			Assert::string($nullableSign);
			Assert::true($nullableSign === '' || $nullableSign === '?');

			$this->indentation = $indentation;
			$this->nullableSign = $nullableSign;
			$this->typeIndentation = $typeIndentation;
			$this->type = $type;
		}


		/**
		 * @return string
		 */
		public function toString()
		{
			if ($this->nullableSign !== '') {
				return $this->indentation . $this->nullableSign . $this->typeIndentation . $this->type;
			}

			return $this->indentation . $this->type;
		}


		/**
		 * @return self
		 */
		public static function parse(NodeParser $parser)
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
