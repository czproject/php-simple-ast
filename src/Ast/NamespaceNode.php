<?php

	namespace CzProject\PhpSimpleAst\Ast;


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
		 * @return self
		 */
		public static function parse(NodeParser $parser)
		{
			$keyword = $parser->consumeTokenAsText(T_NAMESPACE);
			$parser->consumeWhitespace();
			$name = NULL;
			$blockOpener = '';
			$inBrackets = FALSE;

			if ($parser->isCurrent('{')) { // global namespace
				$blockOpener = $parser->flushIndentation() . $parser->consumeTokenAsText('{');
				$inBrackets = TRUE;

			} else { // named namespace
				$name = Name::parse($parser->createSubParser());
				$blockOpener = $parser->tryConsumeAllTokensAsText(T_WHITESPACE);

				if ($parser->isCurrent(';')) {
					$blockOpener .= $parser->consumeTokenAsText(';');

				} elseif ($parser->isCurrent('{')) {
					$blockOpener .= $parser->consumeTokenAsText('{');
					$inBrackets = TRUE;

				} else {
					$parser->errorUnknowToken('Broken namespace definition');
				}
			}

			// namespace body
			$blockCloser = '';

			while ($parser->hasToken()) {
				$child = NULL;

				if ($parser->isCurrent(T_CLOSE_TAG)) {
					break;

				} elseif (!$inBrackets && $parser->isCurrent(T_NAMESPACE)) {
					break;

				} elseif ($parser->isCurrent(T_DOUBLE_COLON)) { // static call or property/constant
					$parser->consumeAsUnknowContent(T_DOUBLE_COLON);
					$parser->tryConsumeAsUnknowContent(T_CLASS);

				} elseif ($parser->isCurrent(T_CLASS)) {
					$child = ClassNode::parse($parser->createSubParser());

				} elseif ($parser->isCurrent(T_TRAIT, T_INTERFACE)) {
					$parser->consumeUnknow();

					while ($parser->hasToken() && !$parser->isCurrent('{', ';')) {
						$parser->consumeUnknow();
					}

				} elseif ($parser->isCurrent(T_NEW)) {
					$parser->consumeUnknow();

					while ($parser->hasToken() && !$parser->isCurrent('(', ';')) {
						$parser->consumeUnknow();
					}
				}

				$parser->onChild($child);
			}

			$parser->close();

			return new self(
				$parser->getNodeIndentation(),
				$keyword,
				$name,
				$blockOpener,
				$parser->getChildren(),
				$blockCloser
			);
		}
	}
