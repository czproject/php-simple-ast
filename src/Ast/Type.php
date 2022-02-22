<?php

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class Type
	{
		/** @var string */
		private $indentation;

		/** @var NullableName[] */
		private $names;


		/**
		 * @param string $indentation
		 * @param NullableName[] $names
		 */
		public function __construct($indentation, array $names)
		{
			Assert::string($indentation);
			Assert::true(count($names) > 0, 'Names cannot be empty.');

			$this->indentation = $indentation;
			$this->names = $names;
		}


		public function toString()
		{
			$s = $this->indentation;

			foreach ($this->names as $name) {
				$s .= $name->toString();
			}

			return $s;
		}


		/**
		 * @return self
		 */
		public static function parse(NodeParser $parser)
		{
			$names = [];
			$names[] = NullableName::parse($parser->createSubParser());
			$parser->tryConsumeWhitespace();

			while ($parser->isCurrent('&', '|')) {
				$parser->consumeAsIndentation('&', '|');
				$parser->tryConsumeWhitespace();
				$names[] = NullableName::parse($parser->createSubParser());
				$parser->tryConsumeWhitespace();
			}

			$parser->close();
			return new self($parser->getNodeIndentation(), $names);
		}
	}
