<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class TraitNode implements INode
	{
		/** @var string */
		private $indentation;

		/** @var string */
		private $keyword;

		/** @var Name */
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
		 * @param INode[] $children
		 * @param string $blockCloser
		 */
		public function __construct(
			$indentation,
			$keyword,
			Name $name,
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
			$keyword = $parser->consumeTokenAsText(T_TRAIT);
			$parser->tryConsumeWhitespace();
			$blockOpener = '';

			$name = Name::parse($parser->createSubParser());
			$parser->tryConsumeWhitespace();

			$parser->tryConsumeWhitespace();
			$blockOpener = $parser->flushIndentation() . $parser->consumeTokenAsText('{');
			$parser->tryConsumeWhitespace();

			// namespace body
			$blockCloser = '';

			while ($parser->hasToken()) {
				$child = NULL;

				if ($parser->isCurrent('}')) {
					$blockCloser = $parser->flushIndentation() . $parser->consumeTokenAsText('}');
					break;

				} elseif ($parser->isCurrent(T_PUBLIC, T_PROTECTED, T_PRIVATE, T_STATIC, T_ABSTRACT, T_FINAL)) {
					$modifiers = Modifiers::parse($parser->createSubParser());

					if ($parser->isCurrent(T_FUNCTION)) {
						$child = MethodNode::parse($modifiers, $parser->createSubParser());

					} elseif ($parser->isCurrent(T_VARIABLE)) {
						$child = PropertyNode::parse($modifiers, $parser->createSubParser());

					} else {
						$parser->errorUnknowToken();
					}

				} elseif ($parser->isCurrent(T_FUNCTION)) {
					$child = MethodNode::parse(Modifiers::empty($parser->flushIndentation()), $parser->createSubParser());

				} elseif ($parser->isCurrent(T_USE)) { // trait
					$child = TraitUseNode::parse($parser->createSubParser());

				} elseif ($parser->isCurrent(T_COMMENT)) {
					$child = CommentNode::parse($parser->createSubParser());

				} elseif ($parser->isCurrent(T_DOC_COMMENT)) {
					$child = PhpDocNode::parse($parser->createSubParser());

				} elseif($parser->isCurrent(T_WHITESPACE)) {
					// nothing

				} else {
					$parser->errorUnknowToken();
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
