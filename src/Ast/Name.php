<?php

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;
	use CzProject\PhpSimpleAst\Lexer;


	class Name
	{
		/** @var string */
		private $indentation;

		/** @var string */
		private $name;


		/**
		 * @param string $indentation
		 * @param string $name
		 */
		public function __construct($indentation, $name)
		{
			Assert::string($name);
			Assert::true($name !== '');
			Assert::string($indentation);

			$this->name = $name;
			$this->indentation = $indentation;
		}


		/**
		 * @return string
		 */
		public function getName()
		{
			return $this->name;
		}


		/**
		 * @param  string $name
		 * @return void
		 */
		public function setName($name)
		{
			// TODO: check name syntax
			$this->name = $name;
		}


		public function toString()
		{
			return $this->indentation . $this->name;
		}


		/**
		 * @param  string $newName
		 * @return self
		 */
		public static function fromName(self $name = NULL, $newName)
		{
			return new self(
				$name !== NULL ? $name->indentation : ' ',
				$newName
			);
		}


		/**
		 * @param  string $indentation
		 * @return self
		 */
		public static function parse($indentation, Lexer\Stream $stream)
		{
			$name = '';

			if (PHP_VERSION_ID >= 80000) {
				if ($stream->isCurrent(T_STRING, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED)) {
					$name = $stream->consumeTokenAsText(T_STRING, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED);
				}

			} else {
				if ($stream->isCurrent(T_STRING)) {
					$name .= $stream->consumeTokenAsText(T_STRING);
				}

				while ($stream->isCurrent(T_NS_SEPARATOR)) {
					$name .= $stream->consumeTokenAsText(T_NS_SEPARATOR);
					$stream->tryConsumeAllTokens(T_WHITESPACE); // TODO: invalid from PHP 8.0+

					$name .= $stream->consumeTokenAsText(T_STRING);
				}
			}

			if ($name === '') {
				throw new \CzProject\PhpSimpleAst\InvalidStateException('Missing name.');
			}

			return new self($indentation, $name);
		}
	}
