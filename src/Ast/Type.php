<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class Type
	{
		/** @var string */
		private $indentation;

		/** @var NamedType[] */
		private $types;


		/**
		 * @param string $indentation
		 * @param NamedType[] $types
		 */
		public function __construct($indentation, array $types)
		{
			Assert::string($indentation);
			Assert::true(count($types) > 0, 'Types cannot be empty.');

			$this->indentation = $indentation;
			$this->types = $types;
		}


		/**
		 * @return string
		 */
		public function toString()
		{
			$s = $this->indentation;

			foreach ($this->types as $name) {
				$s .= $name->toString();
			}

			return $s;
		}


		/**
		 * @return self
		 */
		public static function parse(NodeParser $parser)
		{
			$types = [];
			$types[] = NamedType::parse($parser->createSubParser());
			$parser->tryConsumeWhitespace();

			while ($parser->isCurrent('&', '|')) {
				$parser->consumeAsIndentation('&', '|');
				$parser->tryConsumeWhitespace();
				$types[] = NamedType::parse($parser->createSubParser());
				$parser->tryConsumeWhitespace();
			}

			$parser->close();
			return new self($parser->getNodeIndentation(), $types);
		}
	}
