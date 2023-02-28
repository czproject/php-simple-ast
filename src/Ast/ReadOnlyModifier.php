<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;
	use CzProject\PhpSimpleAst\Lexer\PhpToken;


	class ReadOnlyModifier implements IPropertyModifier
	{
		/** @var string */
		private $indentation;

		/** @var string */
		private $modifier;


		/**
		 * @param string $indentation
		 * @param string $modifier
		 */
		public function __construct($indentation, $modifier)
		{
			Assert::string($indentation);
			Assert::string($modifier);

			$lowerValue = strtolower($modifier);
			Assert::true($lowerValue === 'readonly');

			$this->indentation = $indentation;
			$this->modifier = $modifier;
		}


		public function toString()
		{
			return $this->indentation . $this->modifier;
		}


		/**
		 * @return self
		 */
		public static function parse(NodeParser $parser)
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
			$modifier = $parser->consumeTokenAsText(PhpToken::T_READONLY());
			$parser->close();
			return new self($nodeIndentation, $modifier);
		}
	}
