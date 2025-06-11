<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Lexer;

	use Nette\Utils\Strings;


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


		public function hasToken(): bool
		{
			return isset($this->tokens[$this->position]);
		}


		public function getCurrent(): PhpToken
		{
			if (!isset($this->tokens[$this->position])) {
				throw new \CzProject\PhpSimpleAst\OutOfRangeException('No token here.');
			}

			return $this->tokens[$this->position];
		}


		public function getNext(int $position = 0): ?PhpToken
		{
			$position = $this->position + max(0, $position) + 1;

			if (!isset($this->tokens[$position])) {
				return NULL;
			}

			return $this->tokens[$position];
		}


		public function next(): ?PhpToken
		{
			if (!isset($this->tokens[$this->position])) {
				return NULL;
			}

			$next = $this->tokens[$this->position];
			$this->nextPosition();
			return $next;
		}


		public function getTypeName(int|string $type): string
		{
			return is_int($type) ? token_name($type) : $type;
		}


		private function nextPosition(): void
		{
			if (($this->position + 1) > count($this->tokens)) {
				throw new \CzProject\PhpSimpleAst\InvalidStateException('There no next position.');
			}

			$this->position++;
		}


		public static function fromSource(string $str): self
		{
			$tokens = [];
			$line = 0;
			$whitespaceToMerge = NULL;

			foreach (token_get_all($str) as $position => $token) {
				if ($whitespaceToMerge !== NULL) {
					if (is_array($token) && $token[0] === $whitespaceToMerge->getType()) {
						$token[1] = $whitespaceToMerge->toString() . $token[1];
						$token[2] = $whitespaceToMerge->getLine();

					} else {
						$tokens[] = $whitespaceToMerge;
					}

					$whitespaceToMerge = NULL;
				}

				if (is_string($token)) {
					$tokens[] = new PhpToken($token, $token, $position, $line);

				} else {
					$line = $token[2];

					if ($token[0] === T_COMMENT && ($match = Strings::match($token[1], '~[\\n\\r]+$~'))) {
						assert(is_array($match) && isset($match[0]) && is_string($match[0]));
						$whitespaceToMerge = new PhpToken(T_WHITESPACE, $match[0], $position + 1, $line);
						$token[1] = rtrim($token[1], "\n\r");
					}

					$tokens[] = new PhpToken($token[0], $token[1], $position, $line);
				}
			}

			if ($whitespaceToMerge !== NULL) {
				$tokens[] = $whitespaceToMerge;
			}

			return new self($tokens);
		}
	}
