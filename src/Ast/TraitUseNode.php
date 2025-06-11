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
		 * @param TraitImport[] $imports
		 */
		public function __construct(
			string $indentation,
			string $keyword,
			array $imports,
			string $closer
		)
		{
			Assert::true($closer === '' || $closer === ';', 'Invalid closer.');

			$this->indentation = $indentation;
			$this->keyword = $keyword;
			$this->imports = $imports;
			$this->closer = $closer;
		}


		public function toString(): string
		{
			$s = $this->indentation . $this->keyword;

			foreach ($this->imports as $import) {
				$s .= $import->toString();
			}

			$s .= $this->closer;
			return $s;
		}


		public static function parse(NodeParser $parser): self
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
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
				$nodeIndentation,
				$keyword,
				$imports,
				$closer
			);
		}
	}
