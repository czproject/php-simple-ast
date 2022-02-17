<?php

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;
	use CzProject\PhpSimpleAst\Lexer;


	class NamespaceNode implements INode
	{
		/** @var string */
		private $indentation;

		/** @var string */
		private $keyword;

		/** @var Name|NULL */
		private $name;

		/** @var string */
		private $blockOpener;

		/** @var INode[] */
		private $children;

		/** @var string */
		private $blockCloser;


		/**
		 * @param string $indentation
		 * @param string $keyword
		 * @param string $blockOpener
		 * @param string $children
		 * @param string $blockCloser
		 */
		public function __construct(
			$indentation,
			$keyword,
			Name $name = NULL,
			$blockOpener,
			array $children,
			$blockCloser
		)
		{
			$this->indentation = $indentation;
			$this->keyword = $keyword;
			$this->name = $name;
			$this->blockOpener = $blockOpener;
			$this->children = $children;
			$this->blockCloser = $blockCloser;
		}


		/**
		 * @return string|NULL
		 */
		public function getName()
		{
			return $this->name !== NULL ? $this->name->getName() : NULL;
		}


		/**
		 * @param  string|NULL $name
		 * @return void
		 */
		public function setName($name)
		{
			$this->name = Name::fromName($this->name, $name);
		}


		public function toString()
		{
			$s = $this->indentation . $this->keyword;

			if ($this->name !== NULL) {
				$s .= $this->name->toString();
			}

			$s .= $this->blockOpener;

			foreach ($this->children as $child) {
				$s .= $child->toString();
			}

			return $s . $this->blockCloser;
		}


		/**
		 * @param  string $indentation
		 * @return self
		 */
		public static function parse($indentation, Lexer\Stream $stream)
		{
			$keyword = $stream->consumeTokenAsText(T_NAMESPACE);
			$prefixOfName = $stream->consumeAllTokensAsText(T_WHITESPACE);
			$name = NULL;
			$blockOpener = '';
			$inBrackets = FALSE;

			if ($stream->isCurrent('{')) { // global namespace
				$blockOpener = $prefixOfName;
				$blockOpener .= $stream->consumeTokenAsText('{');
				$prefixOfName = '';
				$inBrackets = TRUE;

			} else { // named namespace
				$name = Name::parse($prefixOfName, $stream);
				$blockOpener = $stream->tryConsumeAllTokensAsText(T_WHITESPACE);

				if ($stream->isCurrent(';')) {
					$blockOpener .= $stream->consumeTokenAsText(';');

				} elseif ($stream->isCurrent('{')) {
					$blockOpener .= $stream->consumeTokenAsText('{');
					$inBrackets = TRUE;

				} else {
					$stream->unknowToken('Broken namespace definition');
				}
			}

			// namespace body
			$children = [];
			$blockCloser = '';
			$unknowTokens = [];

			while ($stream->hasToken()) {
				$child = NULL;

				if ($stream->isCurrent(T_CLOSE_TAG)) {
					break;
				}

				if ($child !== NULL) {
					if (count($unknowTokens) > 0) {
						$children[] = UnknowNode::fromTokens($unknowTokens);
						$unknowTokens = [];
					}

					$children[] = $child;

				} else {
					$unknowTokens[] = $stream->consumeAnything();
				}
			}

			if (count($unknowTokens) > 0) {
				$children[] = UnknowNode::fromTokens($unknowTokens);
			}

			return new self(
				$indentation,
				$keyword,
				$name,
				$blockOpener,
				$children,
				$blockCloser
			);
		}
	}
