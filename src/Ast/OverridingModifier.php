<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class OverridingModifier implements IMethodModifier
	{
		/** @var string */
		private $indentation;

		/** @var string */
		private $modifier;


		public function __construct(string $indentation, string $modifier)
		{
			$lowerValue = strtolower($modifier);
			Assert::true($lowerValue === 'abstract' || $lowerValue === 'final');

			$this->indentation = $indentation;
			$this->modifier = $modifier;
		}


		public function isAbstract(): bool
		{
			return strtolower($this->modifier) === 'abstract';
		}


		public function isFinal(): bool
		{
			return strtolower($this->modifier) === 'final';
		}


		public function setAsAbstract(): void
		{
			$this->modifier = 'abstract';
		}


		public function setAsFinal(): void
		{
			$this->modifier = 'final';
		}


		public function toString(): string
		{
			return $this->indentation . $this->modifier;
		}


		public static function parse(NodeParser $parser): self
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
			$modifier = $parser->consumeTokenAsText(T_ABSTRACT, T_FINAL);
			$parser->close();
			return new self($nodeIndentation, $modifier);
		}
	}
