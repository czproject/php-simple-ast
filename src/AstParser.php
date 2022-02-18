<?php

	namespace CzProject\PhpSimpleAst;


	class AstParser
	{
		/**
		 * @param  string $s
		 * @return Ast\PhpString
		 */
		public function parseString($s)
		{
			return Ast\PhpString::parse($this->createParser($s));
		}


		/**
		 * @param  string $path
		 * @return Ast\PhpFile
		 */
		public function parseFile($path)
		{
			return Ast\PhpFile::parse($path, $this->createParser(file_get_contents($path)));
		}


		/**
		 * @param  string $s
		 * @return Ast\NodeParser
		 */
		private function createParser($s)
		{
			$tokens = Lexer\PhpTokens::fromSource($s);
			return new Ast\NodeParser('', new Lexer\Stream($tokens));
		}
	}
