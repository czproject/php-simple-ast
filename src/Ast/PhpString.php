<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;


	class PhpString implements IPhpSource
	{
		/** @var array<INode|IParentNode> */
		private $children;


		/**
		 * @param array<INode|IParentNode> $children
		 */
		public function __construct(array $children)
		{
			$this->children = $children;
		}


		public function getNodes(): array
		{
			return $this->children;
		}


		public function toString(): string
		{
			$s = '';

			foreach ($this->children as $child) {
				$s .= $child->toString();
			}

			return $s;
		}


		public static function parse(NodeParser $parser): self
		{
			$children = [];

			while ($parser->hasToken()) {
				if ($parser->isCurrent(T_INLINE_HTML)) {
					$children[] = new HtmlNode($parser->consumeNodeIndentation() . $parser->consumeAllTokensAsText(T_INLINE_HTML));

				} elseif ($parser->isCurrent(T_OPEN_TAG, T_OPEN_TAG_WITH_ECHO)) {
					$children[] = PhpNode::parse($parser->createSubParser());

				} else {
					$parser->errorUnknowToken();
				}
			}

			$parser->closeAll();
			return new self($children);
		}
	}
