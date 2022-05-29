<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst;


	class Helpers
	{
		public static function extractIndentation(string $indentation): string
		{
			if (($pos = strrpos($indentation, "\n"))) {
				$indentation = substr($indentation, $pos);
			}

			return ltrim($indentation, "\n");
		}


		public static function indent(string $s, string $indentation): string
		{
			return str_replace("\n", "\n" . $indentation, $s);
		}


		public static function unindent(string $s, string $indentation): string
		{
			return str_replace("\n" . $indentation, "\n", $s);
		}
	}
