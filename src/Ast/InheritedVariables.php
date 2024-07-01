<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;


	class InheritedVariables
	{
		/** @var Literal */
		private $keyword;

		/** @var Literal */
		private $opener;

		/** @var InheritedVariable[] */
		private $variables;

		/** @var Literal */
		private $closer;


		/**
		 * @param InheritedVariable[] $variables
		 */
		public function __construct(
			Literal $keyword,
			Literal $opener,
			array $variables,
			Literal $closer
		)
		{
			$this->keyword = $keyword;
			$this->opener = $opener;
			$this->variables = $variables;
			$this->closer = $closer;
		}


		public function toString(): string
		{
			$s = $this->keyword->toString();
			$s .= $this->opener->toString();

			foreach ($this->variables as $variable) {
				$s .= $variable->toString();
			}

			$s .= $this->closer->toString();
			return $s;
		}


		/**
		 * @return self
		 */
		public static function parse(NodeParser $parser)
		{
			$keyword = Literal::parseToken($parser->createSubParser(), T_USE);
			$parser->tryConsumeWhitespace();
			$opener = Literal::parseToken($parser->createSubParser(), '(');
			$inheritedVariables = [];

			do {
				$parser->tryConsumeWhitespace();
				$inheritedVariables[] = InheritedVariable::parse($parser->createSubParser());
				$parser->tryConsumeWhitespace();

			} while (!$parser->isCurrent(')'));

			$closer = Literal::parseToken($parser->createSubParser(), ')');
			$parser->close();

			return new self(
				$keyword,
				$opener,
				$inheritedVariables,
				$closer
			);
		}
	}
