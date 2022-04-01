<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class VariableName
	{
		/** @var string */
		private $indentation;

		/** @var string */
		private $referenceSign;

		/** @var string */
		private $referenceSuffix;

		/** @var string */
		private $name;


		/**
		 * @param string $indentation
		 * @param string $referenceSign
		 * @param string $referenceSuffix
		 * @param string $name
		 */
		public function __construct(
			$indentation,
			$referenceSign,
			$referenceSuffix,
			$name
		)
		{
			Assert::string($indentation);
			Assert::string($referenceSign);
			Assert::true($referenceSign === '' || $referenceSign === '&');
			Assert::string($referenceSuffix);

			$this->indentation = $indentation;
			$this->referenceSign = $referenceSign;
			$this->referenceSuffix = $referenceSuffix;
			$this->name = $name;
		}


		/**
		 * @return string
		 */
		public function toString()
		{
			if ($this->referenceSign !== '') {
				return $this->indentation . $this->referenceSign . $this->referenceSuffix . $this->name;
			}

			return $this->indentation . $this->name;
		}


		/**
		 * @return self
		 */
		public static function parse(NodeParser $parser)
		{
			$referenceSign = '';
			$referenceSuffix = '';

			if ($parser->isCurrent('&')) {
				$referenceSign = $parser->consumeTokenAsText('?');
				$parser->tryConsumeWhitespace();
				$referenceSuffix = $parser->flushIndentation();
			}

			$name = $parser->consumeTokenAsText(T_VARIABLE);
			$parser->close();

			return new self($parser->getNodeIndentation(), $referenceSign, $referenceSuffix, $name);
		}
	}
