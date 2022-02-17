<?php

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;
	use CzProject\PhpSimpleAst\Lexer;


	class NodeBuffer
	{
		/** @var Lexer\Stream */
		private $stream;

		/** @var INode[] */
		private $children = [];

		/** @var string */
		private $indentation = '';

		/** @var string */
		private $unknowContent = '';

		/** @var bool */
		private $closed = FALSE;


		public function __construct(Lexer\Stream $stream)
		{
			$this->stream = $stream;
		}


		/**
		 * @return INode[]
		 */
		public function getChildren()
		{
			Assert::true($this->closed, 'Buffer must be closed.');
			return $this->children;
		}


		public function flushIndentation()
		{
			Assert::false($this->closed, 'Buffer is already closed.');
			$indentation = $this->indentation;
			$this->indentation = '';
			return $indentation;
		}


		/**
		 * @return void
		 */
		public function consumeWhitespace()
		{
			$this->addIndentation($this->stream->consumeAllTokensAsText(T_WHITESPACE));
		}


		/**
		 * @return void
		 */
		public function tryConsumeWhitespace()
		{
			$this->addIndentation($this->stream->tryConsumeAllTokensAsText(T_WHITESPACE));
		}


		/**
		 * @return void
		 */
		public function consumeUnknow()
		{
			$this->addUnknowContent($this->stream->consumeAnythingAsText());
		}


		/**
		 * @return void
		 */
		public function onChild(INode $child = NULL)
		{
			Assert::false($this->closed, 'Buffer is already closed.');

			if ($child !== NULL) {
				$this->tryFlushUnknowContent();
				$this->children[] = $child;

			} else {
				if ($this->stream->isCurrent(T_WHITESPACE)) {
					$this->addIndentation($this->stream->consumeAllTokensAsText(T_WHITESPACE));

				} else {
					$this->consumeUnknow();
				}
			}
		}


		/**
		 * @return void
		 */
		public function close()
		{
			Assert::false($this->closed, 'Buffer is already closed.');
			$this->tryFlushUnknowContent();
			$this->closed = TRUE;
		}


		/**
		 * @param  string $indentation
		 * @return void
		 */
		private function addIndentation($indentation)
		{
			$this->indentation .= $indentation;
		}


		/**
		 * @param  string $unknowContent
		 * @return void
		 */
		private function addUnknowContent($unknowContent)
		{
			$this->unknowContent .= $this->indentation . $unknowContent;
			$this->indentation = '';
		}


		/**
		 * @return void
		 */
		private function tryFlushUnknowContent()
		{
			if ($this->unknowContent !== '' || $this->indentation !== '') {
				$this->children[] = new UnknowNode($this->unknowContent . $this->indentation);
				$this->unknowContent = '';
				$this->indentation = '';
			}
		}
	}
