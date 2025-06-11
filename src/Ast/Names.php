<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class Names
	{
		/** @var string */
		private $indentation;

		/** @var Name[] */
		private $names;


		/**
		 * @param Name[] $names
		 */
		public function __construct(string $indentation, array $names)
		{
			Assert::true(count($names) > 0, 'Names cannot be empty.');

			$this->indentation = $indentation;
			$this->names = $names;
		}


		public function toString(): string
		{
			$s = $this->indentation;

			foreach ($this->names as $name) {
				$s .= $name->toString();
			}

			return $s;
		}


		public static function parse(NodeParser $parser): self
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
			$names = [];
			$names[] = Name::parse($parser->createSubParser());
			$parser->tryConsumeWhitespace();

			while ($parser->isCurrent(',')) {
				$parser->consumeAsIndentation(',');
				$parser->tryConsumeWhitespace();
				$names[] = Name::parse($parser->createSubParser());
				$parser->tryConsumeWhitespace();
			}

			$parser->close();
			return new self($nodeIndentation, $names);
		}
	}
