<?php

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;
	use CzProject\PhpSimpleAst\Lexer;


	class UnknowNode implements INode
	{
		/** @var string */
		private $content;


		/**
		 * @param string $content
		 */
		public function __construct($content)
		{
			Assert::string($content);
			Assert::true($content !== '', 'Missing content.');
			$this->content = $content;
		}


		public function toString()
		{
			return $this->content;
		}


		/**
		 * @param  Lexer\PhpToken[] $tokens
		 * @return self
		 */
		public static function fromTokens(array $tokens)
		{
			Assert::true(count($tokens) > 0, 'Missing tokens.');
			$s = '';

			foreach ($tokens as $token) {
				$s .= $token->toString();
			}

			return new self($s);
		}
	}
