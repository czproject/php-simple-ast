<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class CommentNode implements INode
	{
		/** @var string */
		private $indentation;

		/** @var string */
		private $content;


		public function __construct(
			string $indentation,
			string $content
		)
		{
			Assert::true($content !== '', 'Missing content.');

			$this->indentation = $indentation;
			$this->content = $content;
		}


		public function toString(): string
		{
			return $this->indentation . $this->content;
		}


		public static function parse(NodeParser $parser): self
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
			$content = $parser->consumeTokenAsText(T_COMMENT);
			$parser->close();

			return new self(
				$nodeIndentation,
				$content
			);
		}
	}
