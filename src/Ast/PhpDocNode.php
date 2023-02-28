<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;
	use CzProject\PhpSimpleAst\Helpers;


	class PhpDocNode implements INode
	{
		/** @var string */
		private $indentation;

		/** @var string */
		private $content;


		/**
		 * @param string $indentation
		 * @param string $content
		 */
		public function __construct(
			$indentation,
			$content
		)
		{
			Assert::string($indentation);
			Assert::string($content);
			Assert::true($content !== '', 'Missing content.');

			$this->indentation = $indentation;
			$this->content = $content;
		}


		public function getContent(): string
		{
			return Helpers::unindent($this->content, Helpers::extractIndentation($this->indentation));
		}


		public function setContent(string $content): void
		{
			Assert::true($content !== '', 'Missing content.');
			$this->content = Helpers::indent($content, Helpers::extractIndentation($this->indentation));
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
			$nodeIndentation = $parser->consumeNodeIndentation();
			$content = $parser->consumeTokenAsText(T_DOC_COMMENT);
			$parser->close();

			return new self(
				$nodeIndentation,
				$content
			);
		}
	}
