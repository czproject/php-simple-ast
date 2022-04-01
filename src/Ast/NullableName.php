<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class NullableName
	{
		/** @var string */
		private $indentation;

		/** @var string */
		private $nullableSign;

		/** @var Name */
		private $name;


		/**
		 * @param string $indentation
		 * @param string $nullableSign
		 */
		public function __construct($indentation, $nullableSign, Name $name)
		{
			Assert::string($indentation);
			Assert::string($nullableSign);
			Assert::true($nullableSign === '' || $nullableSign === '?');

			$this->indentation = $indentation;
			$this->name = $name;
			$this->nullableSign = $nullableSign;
		}


		/**
		 * @return string
		 */
		public function toString()
		{
			if ($this->nullableSign !== '') {
				return $this->indentation . $this->nullableSign . $this->name->toString();
			}

			return $this->indentation . $this->name->getName();
		}


		/**
		 * @return self
		 */
		public static function parse(NodeParser $parser)
		{
			$nullableSign = '';

			if ($parser->isCurrent('?')) {
				$nullableSign = $parser->consumeTokenAsText('?');
				$parser->tryConsumeWhitespace();
			}

			$name = Name::parse($parser->createSubParser());
			$parser->close();
			return new self($parser->getNodeIndentation(), $nullableSign, $name);
		}
	}
