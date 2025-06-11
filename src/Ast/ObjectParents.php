<?php

	declare(strict_types=1);

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


		public function __construct(string $indentation, string $keyword, Names $implements)
		{
			Assert::true($keyword !== '');

			$this->indentation = $indentation;
			$this->keyword = $keyword;
			$this->implements = $implements;
		}


		public function toString(): string
		{
			return $this->indentation . $this->keyword . $this->implements->toString();
		}


		public static function parse(NodeParser $parser, int|string $keywordToken): self
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
			$keyword = $parser->consumeTokenAsText($keywordToken);
			$parser->consumeWhitespace();
			$implements = Names::parse($parser->createSubParser());
			$parser->close();
			return new self($nodeIndentation, $keyword, $implements);
		}
	}
