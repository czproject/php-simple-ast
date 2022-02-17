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
			$buffer = new NodeBuffer($stream);

			$keyword = $stream->consumeTokenAsText(T_NAMESPACE);
			$buffer->consumeWhitespace();
			$name = NULL;
			$blockOpener = '';
			$inBrackets = FALSE;

			if ($stream->isCurrent('{')) { // global namespace
				$blockOpener = $buffer->flushIndentation() . $stream->consumeTokenAsText('{');
				$inBrackets = TRUE;

			} else { // named namespace
				$name = Name::parse($buffer->flushIndentation(), $stream);
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
			$blockCloser = '';

			while ($stream->hasToken()) {
				$child = NULL;

				if ($stream->isCurrent(T_CLOSE_TAG)) {
					break;

				} elseif (!$inBrackets && $stream->isCurrent(T_NAMESPACE)) {
					break;

				} elseif ($stream->isCurrent(T_CLASS, T_TRAIT, T_INTERFACE)) {
					$buffer->consumeUnknow();

					while ($stream->hasToken() && !$stream->isCurrent('{', ';')) {
						$buffer->consumeUnknow();
					}

				} elseif ($stream->isCurrent(T_NEW)) {
					$buffer->consumeUnknow();

					while ($stream->hasToken() && !$stream->isCurrent('(', ';')) {
						$buffer->consumeUnknow();
					}
				}

				$buffer->onChild($child);
			}

			$buffer->close();

			return new self(
				$indentation,
				$keyword,
				$name,
				$blockOpener,
				$buffer->getChildren(),
				$blockCloser
			);
		}
	}
