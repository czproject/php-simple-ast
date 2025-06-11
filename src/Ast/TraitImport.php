<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;


	class TraitImport implements INode
	{
		/** @var string */
		private $indentation;

		/** @var Name */
		private $name;

		/** @var Literal|NULL */
		private $aliases;


		public function __construct(
			string $indentation,
			Name $name,
			?Literal $aliases = NULL
		)
		{
			$this->indentation = $indentation;
			$this->name = $name;
			$this->aliases = $aliases;
		}


		public function toString(): string
		{
			$s = $this->indentation;
			$s .= $this->name->toString();

			if ($this->aliases !== NULL) {
				$s .= $this->aliases->toString();
			}

			return $s;
		}


		public static function parse(NodeParser $parser): self
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
			$name = Name::parse($parser->createSubParser());
			$parser->tryConsumeWhitespace();
			$aliases = NULL;

			if ($parser->isCurrent('{')) {
				$aliases = Literal::parseBlockExpression($parser->createSubParser());
			}

			$parser->close();

			return new self(
				$nodeIndentation,
				$name,
				$aliases
			);
		}
	}
