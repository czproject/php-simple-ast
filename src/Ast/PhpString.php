<?php

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\PhpSimpleAst\Lexer;


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
		public static function parse(Lexer\Stream $stream)
		{
			$children = [];

			while ($stream->hasToken()) {
				if ($stream->isCurrent(T_INLINE_HTML)) {
					$children[] = new HtmlNode($stream->consumeAllTokensAsText(T_INLINE_HTML));

				} elseif ($stream->isCurrent([T_OPEN_TAG, T_OPEN_TAG_WITH_ECHO])) {
					$children[] = PhpNode::parse($stream);

				} else {
					$stream->unknowToken();
				}
			}

			return new self($children);
		}
	}
