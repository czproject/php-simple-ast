<?php

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\PhpSimpleAst\Lexer;


	class PhpNode implements INode
	{
		/** @var Lexer\PhpToken */
		private $openToken;

		/** @var INode[] */
		private $children;

		/** @var Lexer\PhpToken|NULL */
		private $closeToken;


		/**
		 * @param INode[] $children
		 */
		public function __construct(
			Lexer\PhpToken $openToken,
			array $children,
			Lexer\PhpToken $closeToken = NULL
		)
		{
			$this->openToken = $openToken;
			$this->children = $children;
			$this->closeToken = $closeToken;
		}


		public function toString()
		{
			$s = $this->openToken->toString();

			foreach ($this->children as $child) {
				$s .= $child->toString();
			}

			return $s . ($this->closeToken !== NULL ? $this->closeToken->toString() : '');
		}


		/**
		 * @return self
		 */
		public static function parse(Lexer\Stream $stream)
		{
			$openToken = $stream->consumeToken(T_OPEN_TAG, T_OPEN_TAG_WITH_ECHO);
			$children = [];
			$closeToken = NULL;
			$unknowTokens = [];

			while ($stream->hasToken()) {
				if ($stream->isCurrent(T_CLOSE_TAG)) {
					$closeToken = $stream->consumeToken(T_CLOSE_TAG);
					break;

				} else {
					$unknowTokens[] = $stream->consumeAnything();
				}
			}

			$children[] = new UnknowNode($unknowTokens);
			return new self($openToken, $children, $closeToken);
		}
	}
