<?php

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class ObjectImplements
	{
		/** @var string */
		private $indentation;

		/** @var string */
		private $keyword;

		/** @var Name[] */
		private $implements;


		/**
		 * @param string $indentation
		 * @param string $keyword
		 * @param Name[] $implements
		 */
		public function __construct($indentation, $keyword, array $implements)
		{
			Assert::string($indentation);
			Assert::string($keyword);
			Assert::true($keyword !== '');
			Assert::true(count($implements) > 0, 'Implements cannot be empty.');

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
			$s = $this->indentation . $this->keyword;

			foreach ($this->implements as $implement) {
				$s .= $implement->toString();
			}

			return $s;
		}


		/**
		 * @return self
		 */
		public static function parse(NodeParser $parser)
		{
			$keyword = $parser->consumeTokenAsText(T_IMPLEMENTS);
			$parser->consumeWhitespace();
			$implements[] = Name::parse($parser->createSubParser());
			$parser->tryConsumeWhitespace();

			while ($parser->isCurrent(',')) {
				$parser->consumeAsIndentation(',');
				$parser->tryConsumeWhitespace();
				$implements[] = Name::parse($parser->createSubParser());
				$parser->tryConsumeWhitespace();
			}

			$parser->close();
			return new self($parser->getNodeIndentation(), $keyword, $implements);
		}
	}
