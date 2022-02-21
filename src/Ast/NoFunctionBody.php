<?php

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class NoFunctionBody implements IFunctionBody
	{
		/** @var string */
		private $indentation;

		/** @var string */
		private $content;


		/**
		 * @param string $indentation
		 * @param string $content
		 */
		public function __construct($indentation, $content)
		{
			Assert::string($indentation);
			Assert::string($content);

			$this->indentation = $indentation;
			$this->content = $content;
		}


		public function toString()
		{
			return $this->indentation . $this->content;
		}


		/**
		 * @return self
		 */
		public static function parse(NodeParser $parser)
		{
			$content = $parser->consumeToken(';');
			$parser->close();
			return new self($parser->getNodeIndentation(), $content);
		}
	}
