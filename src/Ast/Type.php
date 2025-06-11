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
		 * @param NamedType[] $types
		 */
		public function __construct(string $indentation, array $types)
		{
			Assert::true(count($types) > 0, 'Types cannot be empty.');

			$this->indentation = $indentation;
			$this->types = $types;
		}


		public function toString(): string
		{
			$s = $this->indentation;

			foreach ($this->types as $name) {
				$s .= $name->toString();
			}

			return $s;
		}


		public static function parse(NodeParser $parser): self
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
			$types = [];
			$types[] = NamedType::parse($parser->createSubParser());
			$parser->tryConsumeWhitespace();

			while ($parser->isCurrent('&', '|') && !$parser->isAhead(T_ELLIPSIS, T_VARIABLE)) {
				$parser->consumeAsIndentation('&', '|');
				$parser->tryConsumeWhitespace();
				$types[] = NamedType::parse($parser->createSubParser());
				$parser->tryConsumeWhitespace();
			}

			$parser->close();
			return new self($nodeIndentation, $types);
		}
	}
