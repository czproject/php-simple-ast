<?php

	declare(strict_types=1);

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


		public function getName(): string
		{
			return $this->extends->getName();
		}


		/**
		 * @return string
		 */
		public function toString()
		{
			return $this->indentation . $this->keyword . $this->extends->toString();
		}


		/**
		 * @return self
		 */
		public static function parse(NodeParser $parser)
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
			$keyword = $parser->consumeTokenAsText(T_EXTENDS);
			$parser->consumeWhitespace();
			$extends = Name::parse($parser->createSubParser());

			$parser->close();
			return new self($nodeIndentation, $keyword, $extends);
		}
	}
