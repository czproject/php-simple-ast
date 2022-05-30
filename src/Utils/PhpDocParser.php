<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Utils;

	use PHPStan\PhpDocParser\Lexer;
	use PHPStan\PhpDocParser\Parser;


	class PhpDocParser
	{
		/** @var Lexer\Lexer */
		private $lexer;

		/** @var Parser\PhpDocParser */
		private $phpDocParser;

		/** @var Parser\TypeParser */
		private $typeParser;

		/** @var self|NULL */
		private static $instance = NULL;


		private function __construct(
			Lexer\Lexer $lexer,
			Parser\PhpDocParser $phpDocParser,
			Parser\TypeParser $typeParser
		)
		{
			$this->lexer = $lexer;
			$this->phpDocParser = $phpDocParser;
			$this->typeParser = $typeParser;
		}


		public function parse(string $docComment): \PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode
		{
			$tokens = new Parser\TokenIterator($this->lexer->tokenize($docComment));
			return $this->phpDocParser->parse($tokens);
		}


		public function parseType(string $input): \PHPStan\PhpDocParser\Ast\Type\TypeNode
		{
			$tokens = new Parser\TokenIterator($this->lexer->tokenize($input));
			return $this->typeParser->parse($tokens);
		}


		public static function getInstance(): self
		{
			if (self::$instance === NULL) {
				$constExprParser = new Parser\ConstExprParser();
				$typeParser = new Parser\TypeParser($constExprParser);
				self::$instance = new self(
					new Lexer\Lexer,
					new Parser\PhpDocParser($typeParser, $constExprParser),
					$typeParser
				);
			}

			return self::$instance;
		}
	}
