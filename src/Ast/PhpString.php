<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;


	class PhpString implements INode
	{
		/** @var INode[] */
		private $children;


		/**
		 * @param INode[] $children
		 */
		public function __construct(array $children)
		{
			$this->children = $children;
		}


		public function toString()
		{
			$s = '';

			foreach ($this->children as $child) {
				$s .= $child->toString();
			}

			return $s;
		}


		/**
		 * @return self
		 */
		public static function parse(NodeParser $parser)
		{
			$children = [];

			while ($parser->hasToken()) {
				if ($parser->isCurrent(T_INLINE_HTML)) {
					$children[] = new HtmlNode($parser->consumeAllTokensAsText(T_INLINE_HTML));

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
