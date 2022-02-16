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
			return Ast\PhpString::parse($this->createStream($s));
		}


		/**
		 * @param  string $path
		 * @return Ast\PhpFile
		 */
		public function parseFile($path)
		{
			return Ast\PhpFile::parse($path, $this->createStream(file_get_contents($path)));
		}


		/**
		 * @param  string $s
		 * @return Lexer\Stream
		 */
		private function createStream($s)
		{
			$tokens = Lexer\PhpTokens::fromSource($s);
			return new Lexer\Stream($tokens);
		}
	}
