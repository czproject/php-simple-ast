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
		 * @param INode[] $children
		 */
		public function __construct(
			string $indentation,
			string $keyword,
			Name $name,
			?ObjectParents $extends,
			string $blockOpener,
			array $children,
			string $blockCloser
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


		public function getName(): string
		{
			return $this->name->getName();
		}


		public function setName(string $name): void
		{
			$this->name = Name::fromName($this->name, $name);
		}


		public function toString(): string
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


		public static function parse(NodeParser $parser): self
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
			$keyword = $parser->consumeTokenAsText(T_INTERFACE);
			$parser->tryConsumeWhitespace();
			$extends = NULL;
			$blockOpener = '';

			$name = Name::parseClassName($parser->createSubParser());
			$parser->tryConsumeWhitespace();

			if ($parser->isCurrent(T_EXTENDS)) {
				$extends = ObjectParents::parse($parser->createSubParser(), T_EXTENDS);
			}

			$parser->tryConsumeWhitespace();
			$parser->tryConsumeAllAsIndentation(T_COMMENT);
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

					} elseif ($parser->isCurrent(T_CONST)) {
						$child = ConstantNode::parse($modifiers, $parser->createSubParser());

					} else {
						$parser->errorUnknowToken();
					}

				} elseif ($parser->isCurrent(T_FUNCTION)) {
					$child = MethodNode::parse($phpDocNode, Modifiers::empty($parser->flushIndentation()), $parser->createSubParser());
					$phpDocNode = NULL;

				} elseif ($parser->isCurrent(T_CONST)) {
					$child = ConstantNode::parse(Modifiers::empty($parser->flushIndentation()), $parser->createSubParser());

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
				$nodeIndentation,
				$keyword,
				$name,
				$extends,
				$blockOpener,
				$parser->getChildren(),
				$blockCloser
			);
		}
	}
