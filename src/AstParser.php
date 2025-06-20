<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst;


	class AstParser
	{
		/**
		 * @return Ast\PhpString
		 */
		public function parseString(string $s)
		{
			return Ast\PhpString::parse($this->createParser($s));
		}


		/**
		 * @return Ast\PhpFile
		 */
		public function parseFile(string $path)
		{
			return Ast\PhpFile::parse($path, $this->createParser((string) file_get_contents($path)));
		}


		/**
		 * @return Ast\NodeParser
		 */
		private function createParser(string $s)
		{
			$tokens = Lexer\PhpTokens::fromSource($s);
			return new Ast\NodeParser('', new Lexer\Stream($tokens));
		}
	}
