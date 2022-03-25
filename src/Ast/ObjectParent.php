<?php

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class ObjectParent
	{
		/** @var string */
		private $indentation;

		/** @var string */
		private $keyword;

		/** @var Name */
		private $extends;


		/**
		 * @param string $indentation
		 * @param string $keyword
		 */
		public function __construct($indentation, $keyword, Name $extends)
		{
			Assert::string($indentation);
			Assert::string($keyword);
			Assert::true($keyword !== '');

			$this->indentation = $indentation;
			$this->keyword = $keyword;
			$this->extends = $extends;
		}


		/**
		 * @return Name
		 */
		public function getExtends()
		{
			return $this->extends;
		}


		public function toString()
		{
			return $this->indentation . $this->keyword . $this->extends->toString();
		}


		/**
		 * @return self
		 */
		public static function parse(NodeParser $parser)
		{
			$keyword = $parser->consumeTokenAsText(T_EXTENDS);
			$parser->consumeWhitespace();
			$extends = Name::parse($parser->createSubParser());

			$parser->close();
			return new self($parser->getNodeIndentation(), $keyword, $extends);
		}
	}
