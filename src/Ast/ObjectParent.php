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


		public function __construct(string $indentation, string $keyword, Name $extends)
		{
			Assert::true($keyword !== '');

			$this->indentation = $indentation;
			$this->keyword = $keyword;
			$this->extends = $extends;
		}


		public function getExtends(): Name
		{
			return $this->extends;
		}


		public function getName(): string
		{
			return $this->extends->getName();
		}


		public function toString(): string
		{
			return $this->indentation . $this->keyword . $this->extends->toString();
		}


		public static function parse(NodeParser $parser): self
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
			$keyword = $parser->consumeTokenAsText(T_EXTENDS);
			$parser->consumeWhitespace();
			$extends = Name::parse($parser->createSubParser());

			$parser->close();
			return new self($nodeIndentation, $keyword, $extends);
		}
	}
