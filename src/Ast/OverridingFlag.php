<?php

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class OverridingFlag
	{
		/** @var string */
		private $indentation;

		/** @var string */
		private $flag;


		/**
		 * @param string $indentation
		 * @param string $flag
		 */
		public function __construct($indentation, $flag)
		{
			Assert::string($indentation);
			Assert::string($flag);

			$lowerValue = strtolower($flag);
			Assert::true($lowerValue === 'abstract' || $lowerValue === 'final');

			$this->indentation = $indentation;
			$this->flag = $flag;
		}


		/**
		 * @return bool
		 */
		public function isAbstract()
		{
			return strtolower($this->flag) === 'abstract';
		}


		/**
		 * @return bool
		 */
		public function isFinal()
		{
			return strtolower($this->flag) === 'final';
		}


		/**
		 * @return void
		 */
		public function setAsAbstract()
		{
			$this->flag = 'abstract';
		}


		/**
		 * @return void
		 */
		public function setAsFinal()
		{
			$this->flag = 'final';
		}


		public function toString()
		{
			return $this->indentation . $this->flag;
		}


		/**
		 * @return self
		 */
		public static function parse(NodeParser $parser)
		{
			$flag = $parser->consumeTokenAsText(T_ABSTRACT, T_FINAL);
			$parser->close();
			return new self($parser->getNodeIndentation(), $flag);
		}
	}
