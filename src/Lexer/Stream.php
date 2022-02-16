<?php

	namespace CzProject\PhpSimpleAst\Lexer;


	class Stream
	{
		/** @var ITokens */
		private $tokens;


		public function __construct(ITokens $tokens)
		{
			$this->tokens = $tokens;
		}


		/**
		 * @return bool
		 */
		public function hasToken()
		{
			return $this->tokens->hasToken();
		}


		/**
		 * @return IToken
		 */
		public function getCurrent()
		{
			return $this->tokens->getCurrent();
		}


		/**
		 * @param  int|string ...$types
		 * @return bool
		 */
		public function isCurrent(...$types)
		{
			$token = $this->tokens->getCurrent();

			foreach ($types as $type) {
				if ($token->isOfType($type)) {
					return TRUE;
				}
			}

			return FALSE;
		}


		/**
		 * @return IToken
		 */
		public function consumeAnything()
		{
			if (!$this->hasToken()) {
				throw new \CzProject\PhpSimpleAst\InvalidStateException('No token to consume.');
			}

			$token = $this->tokens->getCurrent();
			$this->tokens->next();
			return $token;
		}


		/**
		 * @param  int|string ...$type
		 * @return IToken
		 */
		public function consumeToken(...$types)
		{
			if (!$this->isCurrent(...$types)) {
				$token = $this->tokens->getCurrent();
				$currentTokenType = $token->getType();
				$currentTokenText = is_int($currentTokenType) ? (' (text: ' . $token->toString() . ')') : '';
				$currentTokenLine = ' on line' . $token->getLine();
				$currentToken = $this->formatTokenType($currentTokenType);
				$expectedToken = $this->formatTokenType(...$types);
				throw new \CzProject\PhpSimpleAst\InvalidStateException("Invalid token '{$currentToken}'{$currentTokenText}{$currentTokenLine}, expected '{$expectedToken}'.");
			}

			$token = $this->tokens->getCurrent();
			$this->tokens->next();
			return $token;
		}


		/**
		 * @param  int|string ...$type
		 * @return string
		 */
		public function consumeTokenAsText(...$types)
		{
			return $this->consumeToken(...$types)->toString();
		}


		/**
		 * @param  int|string ...$types
		 * @return IToken|NULL
		 */
		public function tryConsumeToken(...$types)
		{
			if ($this->isCurrent($types)) {
				return $this->consumeToken($types);
			}

			return NULL;
		}


		/**
		 * @param  int|string ...$types
		 * @return IToken[]
		 */
		public function consumeAllTokens(...$types)
		{
			$res = [];
			$res[] = $this->consumeToken(...$types);

			while ($this->hasToken() && $this->isCurrent(...$types)) {
				$res[] = $this->consumeToken(...$types);
			}

			return $res;
		}


		/**
		 * @param  int|string ...$types
		 * @return string
		 */
		public function consumeAllTokensAsText(...$types)
		{
			$s = '';

			foreach ($this->consumeAllTokens(...$types) as $token) {
				$s .= $token->toString();
			}

			return $s;
		}


		/**
		 * @param  int|string ...$types
		 * @return IToken[]|NULL
		 */
		public function tryConsumeAllTokens(...$types)
		{
			if ($this->isCurrent(...$types)) {
				return $this->consumeAllTokens(...$types);
			}

			return NULL;
		}


		/**
		 * @return never
		 * @throws \CzProject\PhpSimpleAst\InvalidStateException
		 */
		public function unknowToken()
		{
			throw new \CzProject\PhpSimpleAst\InvalidStateException('Unknow token ' . $this->formatTokenType($this->getCurrent()->getType()));
		}


		/**
		 * @param  int|string ...$types
		 * @return string
		 */
		private function formatTokenType(...$types)
		{
			$tmp = [];

			foreach ($types as $type) {
				$tmp[] = $this->tokens->getTypeName($type);
			}

			return implode('|', $tmp);
		}
	}
