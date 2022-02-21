<?php

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


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
		 * @param string $indentation
		 * @param string $blockOpener
		 * @param INode[] $children
		 * @param string $blockCloser
		 */
		public function __construct(
			$indentation,
			$blockOpener,
			array $children,
			$blockCloser
		)
		{
			Assert::string($indentation);

			$this->indentation = $indentation;
			$this->blockOpener = $blockOpener;
			$this->children = $children;
			$this->blockCloser = $blockCloser;
		}


		public function toString()
		{
			$s = $this->indentation . $this->blockOpener;

			foreach ($this->children as $child) {
				$s .= $child->toString();
			}

			return $s . $this->blockCloser;
		}


		/**
		 * @return self
		 */
		public static function parse(NodeParser $parser)
		{
			$blockOpener = $parser->consumeTokenAsText('{');
			$level = 0;

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

				// } else {
					// $parser->consumeUnknow();
					// continue;
				}

				$parser->onChild($child);
			}

			$blockCloser = $parser->flushIndentation() . $parser->consumeTokenAsText('}');
			$parser->close();

			return new self(
				$parser->getNodeIndentation(),
				$blockOpener,
				$parser->getChildren(),
				$blockCloser
			);
		}
	}