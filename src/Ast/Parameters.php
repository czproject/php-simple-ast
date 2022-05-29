<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


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
		 * @param string $indentation
		 * @param string $opener
		 * @param Parameter[] $parameters
		 * @param string $closer
		 */
		public function __construct(
			$indentation,
			$opener,
			array $parameters,
			$closer
		)
		{
			Assert::string($indentation);
			Assert::string($opener);
			Assert::string($closer);

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


		/**
		 * @return self
		 */
		public static function parse(NodeParser $parser)
		{
			$opener = $parser->consumeTokenAsText('(');
			$parser->tryConsumeWhitespace();
			$parameters = [];

			while (!$parser->isCurrent(')')) {
				$parameters[] = Parameter::parse($parser->createSubParser());
				$parser->tryConsumeWhitespace();

				if ($parser->isCurrent(',')) {
					$parser->consumeAsIndentation(',');
					$parser->tryConsumeWhitespace();
				}
			}

			$closer = $parser->flushIndentation() . $parser->consumeTokenAsText(')');
			$parser->close();

			return new self(
				$parser->getNodeIndentation(),
				$opener,
				$parameters,
				$closer
			);
		}
	}
