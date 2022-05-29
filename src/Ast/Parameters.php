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
		private $arguments;

		/** @var string */
		private $closer;


		/**
		 * @param string $indentation
		 * @param string $opener
		 * @param Parameter[] $arguments
		 * @param string $closer
		 */
		public function __construct(
			$indentation,
			$opener,
			array $arguments,
			$closer
		)
		{
			Assert::string($indentation);
			Assert::string($opener);
			Assert::string($closer);

			$this->indentation = $indentation;
			$this->opener = $opener;
			$this->arguments = $arguments;
			$this->closer = $closer;
		}


		public function toString()
		{
			$s = $this->indentation . $this->opener;

			foreach ($this->arguments as $argument) {
				$s .= $argument->toString();
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
			$arguments = [];

			while (!$parser->isCurrent(')')) {
				$arguments[] = Parameter::parse($parser->createSubParser());
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
				$arguments,
				$closer
			);
		}
	}
