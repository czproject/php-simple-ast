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
			$unknowTokens = [];

			while ($stream->hasToken()) {
				$unknowTokens[] = $stream->consumeAnything();
			}

			if (count($unknowTokens) > 0) {
				$children[] = new UnknowNode($unknowTokens);
			}

			return new self($children);
		}
	}
