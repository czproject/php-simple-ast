<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Lexer;


	class PhpToken implements IToken
	{
		/** @var int|string */
		private $type;

		/** @var string */
		private $text;

		/** @var int */
		private $position;

		/** @var int */
		private $line;


		public function __construct(int|string $type, string $text, int $position, int $line)
		{
			$this->type = $type;
			$this->text = $text;
			$this->position = $position;
			$this->line = $line;
		}


		public function isOfType(int|string $type): bool
		{
			return $this->type === $type;
		}


		public function getType(): int|string
		{
			return $this->type;
		}


		public function toString(): string
		{
			return $this->text;
		}


		public function getPosition(): int
		{
			return $this->position;
		}


		public function getLine(): int
		{
			return $this->line;
		}


		public static function T_READONLY(): int
		{
			return PHP_VERSION_ID >= 80100 ? T_READONLY : -1;
		}


		public static function T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG(): int
		{
			return PHP_VERSION_ID >= 80100 ? T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG : -1;
		}
	}
