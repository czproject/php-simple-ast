<?php

	namespace CzProject\PhpSimpleAst\Ast;


	class ClassNode implements INode
	{
		/** @var string */
		private $indentation;

		/** @var string */
		private $keyword;

		/** @var Name|NULL */
		private $name;

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
		 * @param string $children
		 * @param string $blockCloser
		 */
		public function __construct(
			$indentation,
			$keyword,
			Name $name = NULL,
			ObjectParent $extends = NULL,
			ObjectParents $implements = NULL,
			$blockOpener,
			array $children,
			$blockCloser
		)
		{
			$this->indentation = $indentation;
			$this->keyword = $keyword;
			$this->name = $name;
			$this->extends = $extends;
			$this->implements = $implements;
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
			$parser->consumeWhitespace();
			$name = NULL;
			$extends = NULL;
			$implements = NULL;
			$blockOpener = '';

			if ($parser->isCurrent(T_STRING)) { // class name
				$name = Name::parse($parser->createSubParser());
				$parser->tryConsumeWhitespace();
			}

			if ($parser->isCurrent(T_EXTENDS)) {
				$extends = ObjectParent::parse($parser->createSubParser());
				$parser->tryConsumeWhitespace();
			}

			if ($parser->isCurrent(T_IMPLEMENTS)) {
				$implements = ObjectParents::parse($parser->createSubParser());
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
					$flags = Flags::parse($parser->createSubParser());

					if ($parser->isCurrent(T_FUNCTION)) {
						$child = MethodNode::parse($flags, $parser->createSubParser());

					} else {
						$parser->errorUnknowToken();
					}

				} elseif ($parser->isCurrent(T_FUNCTION)) {
					$child = MethodNode::parse(Flags::empty($parser->flushIndentation()), $parser->createSubParser());

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
				$extends,
				$implements,
				$blockOpener,
				$parser->getChildren(),
				$blockCloser
			);
		}
	}
