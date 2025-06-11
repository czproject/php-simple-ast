<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;


	class FunctionBody implements IFunctionBody
	{
		/** @var string */
		private $indentation;

		/** @var string */
		private $blockOpener;

		/** @var INode[] */
		private $children;

		/** @var string */
		private $blockCloser;


		/**
		 * @param INode[] $children
		 */
		public function __construct(
			string $indentation,
			string $blockOpener,
			array $children,
			string $blockCloser
		)
		{
			$this->indentation = $indentation;
			$this->blockOpener = $blockOpener;
			$this->children = $children;
			$this->blockCloser = $blockCloser;
		}


		public function toString(): string
		{
			$s = $this->indentation . $this->blockOpener;

			foreach ($this->children as $child) {
				$s .= $child->toString();
			}

			return $s . $this->blockCloser;
		}


		public static function parse(NodeParser $parser): self
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
			$blockOpener = $parser->consumeTokenAsText('{');
			$level = 1;

			while ($parser->hasToken()) {
				$child = NULL;

				if ($parser->isCurrent('{', T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES)) {
					$level++;
					$parser->consumeUnknow();
					continue;

				} elseif ($parser->isCurrent('}')) {
					$level--;

					if ($level <= 0) {
						break;

					} else {
						$parser->consumeUnknow();
						continue;
					}

				} elseif ($parser->isCurrent(T_COMMENT)) {
					$child = CommentNode::parse($parser->createSubParser());

				} elseif ($parser->isCurrent(T_DOC_COMMENT)) {
					$child = PhpDocNode::parse($parser->createSubParser());

				// } else {
					// $parser->consumeUnknow();
					// continue;
				}

				$parser->onChild($child);
			}

			$blockCloser = $parser->flushIndentation() . $parser->consumeTokenAsText('}');
			$parser->close();

			return new self(
				$nodeIndentation,
				$blockOpener,
				$parser->getChildren(),
				$blockCloser
			);
		}
	}
