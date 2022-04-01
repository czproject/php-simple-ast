<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class TraitUseNode implements INode
	{
		/** @var string */
		private $indentation;

		/** @var string */
		private $keyword;

		/** @var TraitImport[] */
		private $imports;

		/** @var string */
		private $closer;


		/**
		 * @param string $indentation
		 * @param string $keyword
		 * @param TraitImport[] $imports
		 * @param string $closer
		 */
		public function __construct(
			$indentation,
			$keyword,
			array $imports,
			$closer
		)
		{
			Assert::string($indentation);
			Assert::string($keyword);
			Assert::string($closer);
			Assert::true($closer === '' || $closer === ';', 'Invalid closer.');

			$this->indentation = $indentation;
			$this->keyword = $keyword;
			$this->imports = $imports;
			$this->closer = $closer;
		}


		public function toString()
		{
			$s = $this->indentation . $this->keyword;

			foreach ($this->imports as $import) {
				$s .= $import->toString();
			}

			$s .= $this->closer;
			return $s;
		}


		/**
		 * @return self
		 */
		public static function parse(NodeParser $parser)
		{
			$keyword = $parser->consumeTokenAsText(T_USE);
			$parser->consumeWhitespace();
			$imports = [];
			$closer = '';
			$parser->tryConsumeWhitespace();

			do {
				$imports[] = TraitImport::parse($parser->createSubParser());
				$parser->tryConsumeWhitespace();

				if ($parser->isCurrent(',')) {
					$parser->consumeAsIndentation(',');
					$parser->tryConsumeWhitespace();

				} else {
					break;
				}

			} while (TRUE);

			if ($parser->isCurrent(';')) {
				$closer = $parser->consumeTokenAsText(';');
			}

			$parser->close();

			return new self(
				$parser->getNodeIndentation(),
				$keyword,
				$imports,
				$closer
			);
		}
	}
