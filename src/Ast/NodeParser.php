<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;
	use CzProject\PhpSimpleAst\Lexer;


	class NodeParser
	{
		/** @var string|NULL */
		private $nodeIndentation;

		/** @var Lexer\Stream */
		private $stream;

		/** @var self|NULL */
		private $parentParser;

		/** @var INode[] */
		private $children = [];

		/** @var string */
		private $indentation = '';

		/** @var string */
		private $unknowContent = '';

		/** @var bool */
		private $closed = FALSE;


		/**
		 * @param string $nodeIndentation
		 */
		public function __construct($nodeIndentation, Lexer\Stream $stream, self $parentParser = NULL)
		{
			$this->nodeIndentation = $nodeIndentation;
			$this->stream = $stream;
			$this->parentParser = $parentParser;
		}


		/**
		 * @return string
		 */
		public function consumeNodeIndentation()
		{
			if ($this->nodeIndentation !== NULL) {
				$res = $this->nodeIndentation;
				$this->nodeIndentation = NULL;
				return $res;
			}

			return '';
		}


		/**
		 * @return self
		 */
		public function createSubParser()
		{
			return new self($this->consumeNodeIndentation() . $this->flushIndentation(), $this->stream, $this);
		}


		/**
		 * @return INode[]
		 */
		public function getChildren()
		{
			Assert::true($this->closed, 'Node must be closed.');
			return $this->children;
		}


		/**
		 * @return string
		 */
		public function flushIndentation()
		{
			Assert::false($this->closed, 'Node is already closed.');
			$indentation = $this->indentation;
			$this->indentation = '';
			return $indentation;
		}


		/**
		 * @return bool
		 */
		public function hasToken()
		{
			return $this->stream->hasToken();
		}


		/**
		 * @param  int|string ...$types
		 * @return bool
		 * @phpstan-impure
		 */
		public function isCurrent(...$types)
		{
			return $this->stream->isCurrent(...$types);
		}


		/**
		 * @param  int|string ...$types
		 * @return bool
		 * @phpstan-impure
		 */
		public function isNext(...$types)
		{
			return $this->stream->isNext(...$types);
		}


		/**
		 * @param  int|string ...$types
		 * @return Lexer\IToken
		 */
		public function consumeToken(...$types)
		{
			Assert::null($this->nodeIndentation, 'NodeIndentation must be consumed first.');
			return $this->stream->consumeToken(...$types);
		}


		/**
		 * @param  int|string ...$types
		 * @return string
		 */
		public function consumeTokenAsText(...$types)
		{
			Assert::null($this->nodeIndentation, 'NodeIndentation must be consumed first.');
			return $this->stream->consumeTokenAsText(...$types);
		}


		/**
		 * @param  int|string ...$types
		 * @return string
		 */
		public function consumeAllTokensAsText(...$types)
		{
			Assert::null($this->nodeIndentation, 'NodeIndentation must be consumed first.');
			return $this->stream->consumeAllTokensAsText(...$types);
		}


		/**
		 * @param  int|string ...$types
		 * @return string
		 */
		public function tryConsumeAllTokensAsText(...$types)
		{
			Assert::null($this->nodeIndentation, 'NodeIndentation must be consumed first.');
			return $this->stream->tryConsumeAllTokensAsText(...$types);
		}


		/**
		 * @param  int|string ...$types
		 * @return void
		 */
		public function consumeAsIndentation(...$types)
		{
			if ($this->nodeIndentation !== NULL) {
				$this->addIndentation($this->consumeNodeIndentation());
			}

			$this->addIndentation($this->stream->consumeTokenAsText(...$types));
		}


		/**
		 * @param  int|string ...$types
		 * @return void
		 */
		public function tryConsumeAllAsIndentation(...$types)
		{
			if ($this->nodeIndentation !== NULL) {
				$this->addIndentation($this->consumeNodeIndentation());
			}

			$this->addIndentation($this->stream->tryConsumeAllTokensAsText(...$types));
		}


		/**
		 * @return void
		 */
		public function consumeWhitespace()
		{
			if ($this->nodeIndentation !== NULL) {
				$this->addIndentation($this->consumeNodeIndentation());
			}

			$this->addIndentation($this->stream->consumeAllTokensAsText(T_WHITESPACE));
		}


		/**
		 * @return void
		 */
		public function tryConsumeWhitespace()
		{
			if ($this->nodeIndentation !== NULL) {
				$this->addIndentation($this->consumeNodeIndentation());
			}

			$this->addIndentation($this->stream->tryConsumeAllTokensAsText(T_WHITESPACE));
		}


		/**
		 * @return void
		 */
		public function tryConsumeWhitespaceAndComments()
		{
			if ($this->nodeIndentation !== NULL) {
				$this->addIndentation($this->consumeNodeIndentation());
			}

			$this->addIndentation($this->stream->tryConsumeAllTokensAsText(T_WHITESPACE, T_COMMENT));
		}


		/**
		 * @return void
		 */
		public function consumeUnknow()
		{
			Assert::null($this->nodeIndentation, 'NodeIndentation must be consumed first.');
			$this->addUnknowContent($this->stream->consumeAnythingAsText());
		}


		/**
		 * @param  int|string ...$types
		 * @return void
		 */
		public function consumeAsUnknowContent(...$types)
		{
			Assert::null($this->nodeIndentation, 'NodeIndentation must be consumed first.');
			$this->addUnknowContent($this->stream->consumeTokenAsText(...$types));
		}


		/**
		 * @param  int|string ...$types
		 * @return void
		 */
		public function tryConsumeAsUnknowContent(...$types)
		{
			Assert::null($this->nodeIndentation, 'NodeIndentation must be consumed first.');
			$this->addUnknowContent($this->stream->tryConsumeTokenAsText(...$types));
		}


		/**
		 * @return string
		 */
		public function consumeAnythingAsText()
		{
			Assert::null($this->nodeIndentation, 'NodeIndentation must be consumed first.');
			return $this->stream->consumeAnythingAsText();
		}


		/**
		 * @param  string|NULL $msg
		 * @return never
		 * @throws \CzProject\PhpSimpleAst\InvalidStateException
		 */
		public function errorUnknowToken($msg = NULL)
		{
			$this->stream->errorUnknowToken($msg);
		}


		/**
		 * @return void
		 */
		public function onChild(INode $child = NULL)
		{
			Assert::false($this->closed, 'Node is already closed.');

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
			Assert::false($this->closed, 'Node is already closed.');

			if ($this->parentParser === NULL || !$this->stream->hasToken()) {
				$this->tryFlushUnknowContentAndIndentation();

			} else {
				$this->tryFlushUnknowContent();
				assert($this->parentParser !== NULL); // phpstan hotfix
				$this->parentParser->addIndentation($this->flushIndentation());
			}

			$this->closed = TRUE;
		}


		/**
		 * @return void
		 */
		public function closeAll()
		{
			Assert::false($this->closed, 'Node is already closed.');
			$this->tryFlushUnknowContentAndIndentation();
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
		private function tryFlushUnknowContentAndIndentation()
		{
			if ($this->unknowContent !== '' || $this->indentation !== '') {
				$this->children[] = new UnknowNode($this->unknowContent . $this->indentation);
				$this->unknowContent = '';
				$this->indentation = '';
			}
		}


		/**
		 * @return void
		 */
		private function tryFlushUnknowContent()
		{
			if ($this->unknowContent !== '') {
				$this->children[] = new UnknowNode($this->unknowContent);
				$this->unknowContent = '';
			}
		}
	}
