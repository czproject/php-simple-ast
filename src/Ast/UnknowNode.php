<?php

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\PhpSimpleAst\Lexer;


	class UnknowNode implements INode
	{
		/** @var Lexer\PhpToken[] */
		private $tokens;


		/**
		 * @param Lexer\PhpToken[] $tokens
		 */
		public function __construct(array $tokens)
		{
			$this->tokens = $tokens;
		}


		public function toString()
		{
			$s = '';

			foreach ($this->tokens as $token) {
				$s .= $token->toString();
			}

			return $s;
		}
	}
