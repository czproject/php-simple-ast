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


		/**
		 * @return string
		 */
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
		 * @return self
		 */
		public static function parse(NodeParser $parser)
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
			$name = '';

			if (PHP_VERSION_ID >= 80000) {
				if ($parser->isCurrent(T_STRING, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED, T_NAME_RELATIVE)) {
					$name = $parser->consumeTokenAsText(T_STRING, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED, T_NAME_RELATIVE);
				}

			} else {
				if ($parser->isCurrent(T_NAMESPACE)) { // old T_NAME_RELATIVE
					$name .= $parser->consumeTokenAsText(T_NAMESPACE);
					$parser->tryConsumeWhitespace();
					$name .= $parser->consumeAllTokensAsText(T_NS_SEPARATOR);
				}

				if ($parser->isCurrent(T_STRING)) {
					$name .= $parser->consumeTokenAsText(T_STRING);
				}

				while ($parser->isCurrent(T_NS_SEPARATOR)) {
					$name .= $parser->consumeTokenAsText(T_NS_SEPARATOR);
					$parser->tryConsumeWhitespace(); // TODO: invalid from PHP 8.0+

					$name .= $parser->consumeTokenAsText(T_STRING);
				}
			}

			if ($name === '') {
				$parser->errorUnknowToken('Missing name');
			}

			$parser->close();
			return new self($nodeIndentation, $name);
		}
	}
