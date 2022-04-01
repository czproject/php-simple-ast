<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class FunctionReturnType implements IFunctionBody
	{
		/** @var string */
		private $indentation;

		/** @var bool */
		private $nullable;

		/** @var Type */
		private $type;


		/**
		 * @param string $indentation
		 * @param bool $nullable
		 */
		public function __construct(
			$indentation,
			$nullable,
			Type $type
		)
		{
			Assert::string($indentation);
			Assert::bool($nullable);

			$this->indentation = $indentation;
			$this->nullable = $nullable;
			$this->type = $type;
		}


		public function toString()
		{
			$s = $this->indentation . ($this->nullable ? '?' : '');
			$s .= $this->type->toString();
			return $s;
		}


		/**
		 * @return self
		 */
		public static function parse(NodeParser $parser)
		{
			$parser->consumeAsUnknowContent(':');
			$parser->consumeWhitespace();
			$indentation = $parser->getNodeIndentation() . $parser->flushIndentation();
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
