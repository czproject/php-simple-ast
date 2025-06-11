<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;


	class Parameters implements IFunctionBody
	{
		/** @var string */
		private $indentation;

		/** @var string */
		private $opener;

		/** @var Parameter[] */
		private $parameters;

		/** @var string */
		private $closer;


		/**
		 * @param Parameter[] $parameters
		 */
		public function __construct(
			string $indentation,
			string $opener,
			array $parameters,
			string $closer
		)
		{
			$this->indentation = $indentation;
			$this->opener = $opener;
			$this->parameters = $parameters;
			$this->closer = $closer;
		}


		/**
		 * @return Parameter[]
		 */
		public function getParameters(): array
		{
			return $this->parameters;
		}


		public function toString()
		{
			$s = $this->indentation . $this->opener;

			foreach ($this->parameters as $parameter) {
				$s .= $parameter->toString();
			}

			return $s . $this->closer;
		}


		public static function parse(NodeParser $parser): self
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
			$opener = $parser->consumeTokenAsText('(');
			$parser->tryConsumeWhitespaceAndComments();
			$parameters = [];

			while (!$parser->isCurrent(')')) {
				$parameters[] = Parameter::parse($parser->createSubParser());
				$parser->tryConsumeWhitespaceAndComments();

				if ($parser->isCurrent(',')) {
					$parser->consumeAsIndentation(',');
					$parser->tryConsumeWhitespaceAndComments();
				}
			}

			$closer = $parser->flushIndentation() . $parser->consumeTokenAsText(')');
			$parser->close();

			return new self(
				$nodeIndentation,
				$opener,
				$parameters,
				$closer
			);
		}
	}
