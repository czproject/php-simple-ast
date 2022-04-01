<?php

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class TraitImport implements INode
	{
		/** @var string */
		private $indentation;

		/** @var Name */
		private $name;

		/** @var Literal|NULL */
		private $aliases;


		/**
		 * @param string $indentation
		 */
		public function __construct(
			$indentation,
			Name $name,
			Literal $aliases = NULL
		)
		{
			Assert::string($indentation);

			$this->indentation = $indentation;
			$this->name = $name;
			$this->aliases = $aliases;
		}


		public function toString()
		{
			$s = $this->indentation;
			$s .= $this->name->toString();

			if ($this->aliases !== NULL) {
				$s .= $this->aliases->toString();
			}

			return $s;
		}


		/**
		 * @return self
		 */
		public static function parse(NodeParser $parser)
		{
			$name = Name::parse($parser->createSubParser());
			$parser->tryConsumeWhitespace();
			$aliases = NULL;

			if ($parser->isCurrent('{')) {
				$aliases = Literal::parseBlockExpression($parser->createSubParser());
			}

			$parser->close();

			return new self(
				$parser->getNodeIndentation(),
				$name,
				$aliases
			);
		}
	}
