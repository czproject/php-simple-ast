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


		/**
		 * @param string $indentation
		 * @param string $modifier
		 */
		public function __construct($indentation, $modifier)
		{
			Assert::string($indentation);
			Assert::string($modifier);

			$lowerValue = strtolower($modifier);
			Assert::true($lowerValue === 'abstract' || $lowerValue === 'final');

			$this->indentation = $indentation;
			$this->modifier = $modifier;
		}


		/**
		 * @return bool
		 */
		public function isAbstract()
		{
			return strtolower($this->modifier) === 'abstract';
		}


		/**
		 * @return bool
		 */
		public function isFinal()
		{
			return strtolower($this->modifier) === 'final';
		}


		/**
		 * @return void
		 */
		public function setAsAbstract()
		{
			$this->modifier = 'abstract';
		}


		/**
		 * @return void
		 */
		public function setAsFinal()
		{
			$this->modifier = 'final';
		}


		public function toString()
		{
			return $this->indentation . $this->modifier;
		}


		/**
		 * @return self
		 */
		public static function parse(NodeParser $parser)
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
			$modifier = $parser->consumeTokenAsText(T_ABSTRACT, T_FINAL);
			$parser->close();
			return new self($nodeIndentation, $modifier);
		}
	}
