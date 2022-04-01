<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Lexer;


	class PhpTokens implements ITokens
	{
		/** @var PhpToken[] */
		private $tokens;

		/** @var int */
		private $position = 0;


		/**
		 * @param PhpToken[] $tokens
		 */
		private function __construct(array $tokens)
		{
			if (count($tokens) === 0) {
				throw new \CzProject\PhpSimpleAst\Exception('Tokens cannot be empty.');
			}

			$this->tokens = array_values($tokens);
		}


		/**
		 * @return bool
		 */
		public function hasToken()
		{
			return isset($this->tokens[$this->position]);
		}


		/**
		 * @return PhpToken
		 */
		public function getCurrent()
		{
			if (!isset($this->tokens[$this->position])) {
				throw new \CzProject\PhpSimpleAst\OutOfRangeException('No token here.');
			}

			return $this->tokens[$this->position];
		}


		/**
		 * @return PhpToken|NULL
		 */
		public function next()
		{
			if (!isset($this->tokens[$this->position])) {
				return NULL;
			}

			$next = $this->tokens[$this->position];
			$this->nextPosition();
			return $next;
		}


		public function getTypeName($type)
		{
			return is_int($type) ? token_name($type) : $type;
		}


		/**
		 * @return void
		 */
		private function nextPosition()
		{
			if (($this->position + 1) > count($this->tokens)) {
				throw new \CzProject\PhpSimpleAst\InvalidStateException('There no next position.');
			}

			$this->position++;
		}


		/**
		 * @param  string $str
		 * @return self
		 */
		public static function fromSource($str)
		{
			$tokens = [];
			$line = 0;

			foreach (token_get_all($str) as $position => $token) {
				if (is_string($token)) {
					$tokens[] = new PhpToken($token, $token, $position, $line);

				} else {
					$line = $token[2];
					$tokens[] = new PhpToken($token[0], $token[1], $position, $line);
				}
			}

			return new self($tokens);
		}
	}
