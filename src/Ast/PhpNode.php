<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;


	class PhpNode implements IParentNode
	{
		/** @var string */
		private $openTag;

		/** @var INode[] */
		private $children;

		/** @var string|NULL */
		private $closeTag;


		/**
		 * @param INode[] $children
		 */
		public function __construct(
			string $openTag,
			array $children,
			?string $closeTag
		)
		{
			$this->openTag = $openTag;
			$this->children = $children;
			$this->closeTag = $closeTag;
		}


		public function getNodes(): array
		{
			return $this->children;
		}


		public function toString(): string
		{
			$s = $this->openTag;

			foreach ($this->children as $child) {
				$s .= $child->toString();
			}

			return $s . $this->closeTag;
		}


		public static function parse(NodeParser $parser): self
		{
			$openTag = $parser->consumeNodeIndentation() . $parser->consumeTokenAsText(T_OPEN_TAG, T_OPEN_TAG_WITH_ECHO);
			$closeTag = NULL;

			while ($parser->hasToken()) {
				$child = NULL;

				if ($parser->isCurrent(T_CLOSE_TAG)) {
					$closeTag = $parser->consumeTokenAsText(T_CLOSE_TAG);
					break;

				} elseif ($parser->isCurrent(T_NAMESPACE)) {
					$child = NamespaceNode::parse($parser->createSubParser());

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
				}

				$parser->onChild($child);
			}

			$parser->closeAll();
			return new self($openTag, $parser->getChildren(), $closeTag);
		}
	}
