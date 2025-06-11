<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;


	class FunctionReturnType implements IFunctionBody
	{
		/** @var string */
		private $indentation;

		/** @var bool */
		private $nullable;

		/** @var Type */
		private $type;


		public function __construct(
			string $indentation,
			bool $nullable,
			Type $type
		)
		{
			$this->indentation = $indentation;
			$this->nullable = $nullable;
			$this->type = $type;
		}


		public function toString(): string
		{
			$s = $this->indentation . ($this->nullable ? '?' : '');
			$s .= $this->type->toString();
			return $s;
		}


		public static function parse(NodeParser $parser): self
		{
			$indentation = $parser->consumeNodeIndentation();
			$parser->consumeAsIndentation(':');
			$parser->consumeWhitespace();
			$indentation .= $parser->flushIndentation();
			$nullable = FALSE;

			if ($parser->isCurrent('?')) {
				$nullable = TRUE;
			}

			$type = Type::parse($parser->createSubParser());
			$parser->close();

			return new self(
				$indentation,
				$nullable,
				$type
			);
		}
	}
