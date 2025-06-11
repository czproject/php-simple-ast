<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class Name
	{
		/** @var string */
		private $indentation;

		/** @var string */
		private $name;


		public function __construct(string $indentation, string $name)
		{
			Assert::true($name !== '');

			$this->name = $name;
			$this->indentation = $indentation;
		}


		public function getIndentation(): string
		{
			return $this->indentation;
		}


		public function getName(): string
		{
			return $this->name;
		}


		public function setName(string $name): void
		{
			// TODO: check name syntax
			$this->name = $name;
		}


		public function toString(): string
		{
			return $this->indentation . $this->name;
		}


		public static function fromName(?self $name, string $newName): self
		{
			return new self(
				$name !== NULL ? $name->indentation : ' ',
				$newName
			);
		}


		public static function parseAnything(NodeParser $parser): self
		{
			return new self(
				$parser->consumeNodeIndentation(),
				$parser->consumeAnythingAsText()
			);
		}


		public static function parse(NodeParser $parser): self
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
			$name = '';

			if ($parser->isCurrent(T_STRING, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED, T_NAME_RELATIVE)) {
				$name = $parser->consumeTokenAsText(T_STRING, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED, T_NAME_RELATIVE);
			}

			if ($name === '') {
				$parser->errorUnknowToken('Missing name');
			}

			$parser->close();
			return new self($nodeIndentation, $name);
		}


		public static function parseClassName(NodeParser $parser): self
		{
			$name = self::tryParseClassName($parser);

			if ($name === NULL) {
				$parser->errorUnknowToken('Missing name');
			}

			return $name;
		}


		/**
		 * @return self|NULL
		 */
		public static function tryParseClassName(NodeParser $parser)
		{
			$nodeIndentation = '';
			$name = '';

			if ($parser->isCurrent(T_STRING)) {
				$nodeIndentation = $parser->consumeNodeIndentation();
				$name = $parser->consumeTokenAsText(T_STRING);
			}

			$parser->close();
			return $name !== '' ? new self($nodeIndentation, $name) : NULL;
		}


		/**
		 * @return self|NULL
		 */
		public static function tryParseFunctionName(NodeParser $parser)
		{
			$nodeIndentation = '';
			$name = '';

			if ($parser->isCurrent(T_STRING)) {
				$nodeIndentation = $parser->consumeNodeIndentation();
				$name = $parser->consumeTokenAsText(T_STRING);
			}

			$parser->close();
			return $name !== '' ? new self($nodeIndentation, $name) : NULL;
		}
	}
