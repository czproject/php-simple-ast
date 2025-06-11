<?php

	declare(strict_types=1);

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
		 * @param  int|string ...$types
		 */
		public function isNext(...$types): bool
		{
			$token = $this->tokens->getNext();

			if ($token === NULL) {
				return FALSE;
			}

			foreach ($types as $type) {
				if ($token->isOfType($type)) {
					return TRUE;
				}
			}

			return FALSE;
		}


		/**
		 * @param  array<int|string> $types
		 * @param  array<int|string> $ignoredTypes
		 */
		public function isAhead(array $types, array $ignoredTypes = []): bool
		{
			$pos = 0;
			$found = FALSE;

			while (($token = $this->tokens->getNext($pos)) !== NULL) {
				foreach ($ignoredTypes as $ignoredType) {
					if ($token->isOfType($ignoredType)) {
						$pos++;
						continue 2;
					}
				}

				foreach ($types as $type) {
					if ($token->isOfType($type)) {
						$pos++;
						$found = TRUE;
						continue 2;
					}
				}

				break;
			}

			return $found;
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
		 * @return string
		 */
		public function consumeAnythingAsText()
		{
			return $this->consumeAnything()->toString();
		}


		/**
		 * @param  int|string ...$types
		 * @return IToken
		 */
		public function consumeToken(...$types)
		{
			if (!$this->isCurrent(...$types)) {
				$token = $this->tokens->getCurrent();
				$currentTokenType = $token->getType();
				$currentTokenText = is_int($currentTokenType) ? (' (text: ' . $token->toString() . ')') : '';
				$currentTokenLine = ' on line ' . $token->getLine();
				$currentToken = $this->formatTokenType($currentTokenType);
				$expectedToken = $this->formatTokenType(...$types);
				throw new \CzProject\PhpSimpleAst\InvalidStateException("Invalid token '{$currentToken}'{$currentTokenText}{$currentTokenLine}, expected '{$expectedToken}'.");
			}

			$token = $this->tokens->getCurrent();
			$this->tokens->next();
			return $token;
		}


		/**
		 * @param  int|string ...$types
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
			if ($this->isCurrent(...$types)) {
				return $this->consumeToken(...$types);
			}

			return NULL;
		}


		/**
		 * @param  int|string ...$types
		 * @return string
		 */
		public function tryConsumeTokenAsText(...$types)
		{
			$token = $this->tryConsumeToken(...$types);
			return $token !== NULL ? $token->toString() : '';
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
		 * @param  int|string ...$types
		 * @return string
		 */
		public function tryConsumeAllTokensAsText(...$types)
		{
			$tokens = $this->tryConsumeAllTokens(...$types);

			if ($tokens === NULL) {
				return '';
			}

			$s = '';

			foreach ($tokens as $token) {
				$s .= $token->toString();
			}

			return $s;
		}


		/**
		 * @return never
		 * @throws \CzProject\PhpSimpleAst\InvalidStateException
		 */
		public function errorUnknowToken(?string $msg = NULL)
		{
			$currentToken = $this->getCurrent();
			$line = ' on line ' . $currentToken->getLine();
			throw new \CzProject\PhpSimpleAst\InvalidStateException(($msg !== NULL ? ($msg . ' | ') : '') . 'Unknow token ' . $this->formatTokenType($currentToken->getType()) . $line);
		}


		/**
		 * @return string
		 */
		private function formatTokenType(int|string ...$types)
		{
			$tmp = [];

			foreach ($types as $type) {
				$tmp[] = $this->tokens->getTypeName($type);
			}

			return implode('|', $tmp);
		}
	}
