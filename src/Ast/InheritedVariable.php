<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;


	class InheritedVariable
	{
		/** @var VariableName */
		private $variable;

		/** @var Literal|NULL */
		private $suffix;


		public function __construct(
			VariableName $variable,
			?Literal $suffix
		)
		{
			$this->variable = $variable;
			$this->suffix = $suffix;
		}


		public function toString(): string
		{
			$s = $this->variable->toString();

			if ($this->suffix !== NULL) {
				$s .= $this->suffix->toString();
			}

			return $s;
		}


		public static function parse(NodeParser $parser): self
		{
			$variable = VariableName::parse($parser->createSubParser());
			$parser->tryConsumeWhitespace();
			$suffix = NULL;

			if ($parser->isCurrent(',')) {
				$suffix = Literal::parseToken($parser->createSubParser(), ',');
			}

			$parser->close();

			return new self(
				$variable,
				$suffix
			);
		}
	}
