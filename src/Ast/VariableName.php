<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;
	use Nette\Utils\Strings;


	class VariableName
	{
		/** @var string */
		private $indentation;

		/** @var string */
		private $referenceSign;

		/** @var Literal|NULL */
		private $variadic;

		/** @var Literal */
		private $name;


		public function __construct(
			string $indentation,
			string $referenceSign,
			?Literal $variadic,
			Literal $name
		)
		{
			Assert::true($referenceSign === '' || $referenceSign === '&');
			Assert::true(Strings::startsWith($name->getLiteral(), '$'));

			$this->indentation = $indentation;
			$this->referenceSign = $referenceSign;
			$this->variadic = $variadic;
			$this->name = $name;
		}


		public function getName(): string
		{
			return Strings::substring($this->name->getLiteral(), 1);
		}


		public function hasReference(): bool
		{
			return $this->referenceSign !== '';
		}


		public function isVariadic(): bool
		{
			return $this->variadic !== NULL;
		}


		/**
		 * @return string
		 */
		public function toString()
		{
			$s = $this->indentation;

			if ($this->hasReference()) {
				$s .= $this->referenceSign;
			}

			if ($this->variadic !== NULL) {
				$s .= $this->hasReference() ? $this->variadic->toString() : $this->variadic->getLiteral();
			}

			$s .= ($this->hasReference() || $this->variadic !== NULL) ? $this->name->toString() : $this->name->getLiteral();
			return $s;
		}


		/**
		 * @return self
		 */
		public static function parse(NodeParser $parser)
		{
			$referenceSign = '';

			if ($parser->isCurrent('&')) {
				$referenceSign = $parser->consumeTokenAsText('&');
				$parser->tryConsumeWhitespace();
			}

			$variadic = NULL;

			if ($parser->isCurrent(T_ELLIPSIS)) {
				$variadic = Literal::parseToken($parser->createSubParser(), T_ELLIPSIS);
				$parser->tryConsumeWhitespace();
			}

			$name = Literal::parseToken($parser->createSubParser(), T_VARIABLE);
			$parser->close();

			return new self($parser->getNodeIndentation(), $referenceSign, $variadic, $name);
		}
	}
