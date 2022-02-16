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
		 * @param  int|string|array<int|string> $type
		 * @return bool
		 */
		public function isCurrent($type)
		{
			$token = $this->tokens->getCurrent();

			if (is_array($type)) {
				foreach ($type as $t) {
					if ($token->isOfType($t)) {
						return TRUE;
					}
				}

				return FALSE;
			}

			return $token->isOfType($type);
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
		 * @param  int|string|array<int|string> $type
		 * @return IToken
		 */
		public function consumeToken($type)
		{
			if (!$this->isCurrent($type)) {
				$token = $this->tokens->getCurrent();
				$currentTokenType = $token->getType();
				$currentTokenText = is_int($currentTokenType) ? (' (text: ' . $token->toString() . ')') : '';
				$currentTokenLine = ' on line' . $token->getLine();
				$currentToken = $this->formatTokenType($currentTokenType);
				$expectedToken = $this->formatTokenType($type);
				throw new \CzProject\PhpSimpleAst\InvalidStateException("Invalid token '{$currentToken}'{$currentTokenText}{$currentTokenLine}, expected '{$expectedToken}'.");
			}

			$token = $this->tokens->getCurrent();
			$this->tokens->next();
			return $token;
		}


		/**
		 * @param  int|string $type
		 * @return IToken|NULL
		 */
		public function tryConsumeToken($type)
		{
			if ($this->isCurrent($type)) {
				return $this->consumeToken($type);
			}

			return NULL;
		}


		/**
		 * @param  int|string $type
		 * @return IToken[]
		 */
		public function consumeAllTokens($type)
		{
			$res = [];
			$res[] = $this->consumeToken($type);

			while ($this->hasToken() && $this->isCurrent($type)) {
				$res[] = $this->consumeToken($type);
			}

			return $res;
		}


		/**
		 * @param  int|string $type
		 * @return string
		 */
		public function consumeAllTokensAsText($type)
		{
			$s = '';

			foreach ($this->consumeAllTokens($type) as $token) {
				$s .= $token->toString();
			}

			return $s;
		}


		/**
		 * @param  int|string $type
		 * @return IToken[]|NULL
		 */
		public function tryConsumeAllTokens($type)
		{
			if ($this->isCurrent($type)) {
				return $this->consumeAllTokens($type);
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
		 * @param  int|string|array<int|string> $type
		 * @return string
		 */
		private function formatTokenType($type)
		{
			if (is_array($type)) {
				$tmp = [];

				foreach ($type as $t) {
					$tmp[] = $this->tokens->getTypeName($t);
				}

				return implode('|', $tmp);
			}

			return $this->tokens->getTypeName($type);
		}
	}
