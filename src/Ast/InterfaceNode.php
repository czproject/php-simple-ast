<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;


	class InterfaceNode implements INode
	{
		/** @var string */
		private $indentation;

		/** @var string */
		private $keyword;

		/** @var Name */
		private $name;

		/** @var ObjectParents|NULL */
		private $extends;

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
		 * @param INode[] $children
		 * @param string $blockCloser
		 */
		public function __construct(
			$indentation,
			$keyword,
			Name $name,
			ObjectParents $extends = NULL,
			$blockOpener,
			array $children,
			$blockCloser
		)
		{
			$this->indentation = $indentation;
			$this->keyword = $keyword;
			$this->name = $name;
			$this->extends = $extends;
			$this->blockOpener = $blockOpener;
			$this->children = $children;
			$this->blockCloser = $blockCloser;
		}


		/**
		 * @return string
		 */
		public function getName()
		{
			return $this->name->getName();
		}


		/**
		 * @param  string $name
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

			if ($this->extends !== NULL) {
				$s .= $this->extends->toString();
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
			$keyword = $parser->consumeTokenAsText(T_INTERFACE);
			$parser->tryConsumeWhitespace();
			$extends = NULL;
			$blockOpener = '';

			$name = Name::parse($parser->createSubParser());
			$parser->tryConsumeWhitespace();

			if ($parser->isCurrent(T_EXTENDS)) {
				$extends = ObjectParents::parse($parser->createSubParser(), T_EXTENDS);
			}

			$parser->tryConsumeWhitespace();
			$blockOpener = $parser->flushIndentation() . $parser->consumeTokenAsText('{');
			$parser->tryConsumeWhitespace();

			// namespace body
			$blockCloser = '';
			$phpDocNode = NULL;

			while ($parser->hasToken()) {
				$child = NULL;
				$isPhpDoc = FALSE;

				if ($parser->isCurrent('}')) {
					$blockCloser = $parser->flushIndentation() . $parser->consumeTokenAsText('}');
					break;

				} elseif ($parser->isCurrent(T_PUBLIC, T_STATIC)) {
					$modifiers = Modifiers::parse($parser->createSubParser());

					if ($parser->isCurrent(T_FUNCTION)) {
						$child = MethodNode::parse($phpDocNode, $modifiers, $parser->createSubParser());
						$phpDocNode = NULL;

					} else {
						$parser->errorUnknowToken();
					}

				} elseif ($parser->isCurrent(T_FUNCTION)) {
					$child = MethodNode::parse($phpDocNode, Modifiers::empty($parser->flushIndentation()), $parser->createSubParser());
					$phpDocNode = NULL;

				} elseif ($parser->isCurrent(T_COMMENT)) {
					$child = CommentNode::parse($parser->createSubParser());

				} elseif ($parser->isCurrent(T_DOC_COMMENT)) {
					$phpDocNode = PhpDocNode::parse($parser->createSubParser());
					$isPhpDoc = TRUE;

				} elseif($parser->isCurrent(T_WHITESPACE)) {
					// nothing

				} else {
					$parser->errorUnknowToken();
				}

				if ($phpDocNode !== NULL && !$isPhpDoc) {
					$parser->onChild($phpDocNode);
					$phpDocNode = NULL;
				}

				$parser->onChild($child);
			}

			$parser->close();

			return new self(
				$parser->getNodeIndentation(),
				$keyword,
				$name,
				$extends,
				$blockOpener,
				$parser->getChildren(),
				$blockCloser
			);
		}
	}
