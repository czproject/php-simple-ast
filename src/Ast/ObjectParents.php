<?php

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class ObjectParents
	{
		/** @var string */
		private $indentation;

		/** @var string */
		private $keyword;

		/** @var Names */
		private $implements;


		/**
		 * @param string $indentation
		 * @param string $keyword
		 */
		public function __construct($indentation, $keyword, Names $implements)
		{
			Assert::string($indentation);
			Assert::string($keyword);
			Assert::true($keyword !== '');

			$this->indentation = $indentation;
			$this->keyword = $keyword;
			$this->implements = $implements;
		}


		/**
		 * @return Name[]
		 */
		public function getImplements()
		{
			return $this->implements;
		}


		public function toString()
		{
			return $this->indentation . $this->keyword . $this->implements->toString();
		}


		/**
		 * @param  int|string $keywordToken
		 * @return self
		 */
		public static function parse(NodeParser $parser, $keywordToken)
		{
			$keyword = $parser->consumeTokenAsText($keywordToken);
			$parser->consumeWhitespace();
			$implements = Names::parse($parser->createSubParser());
			$parser->close();
			return new self($parser->getNodeIndentation(), $keyword, $implements);
		}
	}
