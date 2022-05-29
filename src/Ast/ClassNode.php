<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class ClassNode implements IParentNode
	{
		/** @var string */
		private $indentation;

		/** @var string */
		private $keyword;

		/** @var Name|NULL */
		private $name;

		/** @var Literal|NULL */
		private $constructorValues;

		/** @var ObjectParent|NULL */
		private $extends;

		/** @var ObjectParents|NULL */
		private $implements;

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
			Name $name = NULL,
			Literal $constructorValues = NULL,
			ObjectParent $extends = NULL,
			ObjectParents $implements = NULL,
			$blockOpener,
			array $children,
			$blockCloser
		)
		{
			if ($name !== NULL) {
				Assert::null($constructorValues);
			}

			$this->indentation = $indentation;
			$this->keyword = $keyword;
			$this->name = $name;
			$this->constructorValues = $constructorValues;
			$this->extends = $extends;
			$this->implements = $implements;
			$this->blockOpener = $blockOpener;
			$this->children = $children;
			$this->blockCloser = $blockCloser;
		}


		public function getNodes()
		{
			return $this->children;
		}


		/**
		 * @return string|NULL
		 */
		public function getName()
		{
			return $this->name !== NULL ? $this->name->getName() : NULL;
		}


		/**
		 * @param  string $name
		 * @return void
		 */
		public function setName($name)
		{
			Assert::true($this->name !== NULL, 'Anonymous class cannot be renamed.');
			$this->name = Name::fromName($this->name, $name);
		}


		public function toString()
		{
			$s = $this->indentation . $this->keyword;

			if ($this->name !== NULL) {
				$s .= $this->name->toString();
			}

			if ($this->constructorValues !== NULL) {
				$s .= $this->constructorValues->toString();
			}

			if ($this->extends !== NULL) {
				$s .= $this->extends->toString();
			}

			if ($this->implements !== NULL) {
				$s .= $this->implements->toString();
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
			$keyword = $parser->consumeTokenAsText(T_CLASS);
			$parser->tryConsumeWhitespace();
			$name = NULL;
			$constructorValues = NULL;
			$extends = NULL;
			$implements = NULL;
			$blockOpener = '';

			if ($parser->isCurrent(T_STRING)) { // class name
				$name = Name::parse($parser->createSubParser());
				$parser->tryConsumeWhitespace();

			} elseif ($parser->isCurrent('(')) {
				$constructorValues = Literal::parseParenthesisExpression($parser->createSubParser());
				$parser->tryConsumeWhitespace();
			}

			if ($parser->isCurrent(T_EXTENDS)) {
				$extends = ObjectParent::parse($parser->createSubParser());
				$parser->tryConsumeWhitespace();
			}

			if ($parser->isCurrent(T_IMPLEMENTS)) {
				$implements = ObjectParents::parse($parser->createSubParser(), T_IMPLEMENTS);
			}

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
				$constructorValues,
				$extends,
				$implements,
				$blockOpener,
				$parser->getChildren(),
				$blockCloser
			);
		}
	}
