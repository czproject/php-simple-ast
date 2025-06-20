<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;


	class NamespaceNode implements IParentNode
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
		 * @param INode[] $children
		 */
		public function __construct(
			string $indentation,
			string $keyword,
			?Name $name,
			string $blockOpener,
			array $children,
			string $blockCloser
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
		 * @return void
		 */
		public function setName(?string $name)
		{
			$this->name = $name !== NULL ? Name::fromName($this->name, $name) : NULL;
		}


		public function getNodes()
		{
			return $this->children;
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


		public static function parse(NodeParser $parser): self
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
			$keyword = $parser->consumeTokenAsText(T_NAMESPACE);
			$parser->consumeWhitespace();
			$parser->tryConsumeAllAsIndentation(T_COMMENT);
			$parser->tryConsumeWhitespace();
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

				} elseif ($inBrackets && $parser->isCurrent('}')) {
					$blockCloser = $parser->flushIndentation() . $parser->consumeTokenAsText('}');
					break;

				} elseif ($parser->isCurrent(T_DOUBLE_COLON)) { // static call or property/constant
					$parser->consumeAsUnknowContent(T_DOUBLE_COLON);
					$parser->tryConsumeAsUnknowContent(T_CLASS);

				} elseif ($parser->isCurrent(T_FUNCTION)) {
					$child = FunctionNode::parse(NULL, $parser->createSubParser());

				} elseif ($parser->isCurrent(T_USE)) {
					$child = UseNode::parse($parser->createSubParser());

				} elseif ($parser->isCurrent(T_CLASS)) {
					$child = ClassNode::parse($parser->createSubParser());

				} elseif ($parser->isCurrent(T_INTERFACE)) {
					$child = InterfaceNode::parse($parser->createSubParser());

				} elseif ($parser->isCurrent(T_TRAIT)) {
					$child = TraitNode::parse($parser->createSubParser());

				} elseif ($parser->isCurrent(T_COMMENT)) {
					$child = CommentNode::parse($parser->createSubParser());

				} elseif ($parser->isCurrent(T_DOC_COMMENT)) {
					$child = PhpDocNode::parse($parser->createSubParser());

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
				$nodeIndentation,
				$keyword,
				$name,
				$blockOpener,
				$parser->getChildren(),
				$blockCloser
			);
		}
	}
