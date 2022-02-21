<?php

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class StaticFlag implements IMethodFlag
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
			Assert::true($lowerValue === 'static');

			$this->indentation = $indentation;
			$this->flag = $flag;
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
			$flag = $parser->consumeTokenAsText(T_STATIC);
			$parser->close();
			return new self($parser->getNodeIndentation(), $flag);
		}
	}