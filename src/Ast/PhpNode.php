<?php

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;
	use CzProject\PhpSimpleAst\Lexer;


	class PhpNode implements INode
	{
		/** @var string */
		private $openTag;

		/** @var INode[] */
		private $children;

		/** @var string|NULL */
		private $closeTag;


		/**
		 * @param string $openTag
		 * @param INode[] $children
		 * @param string $closeTag
		 */
		public function __construct(
			$openTag,
			array $children,
			$closeTag
		)
		{
			Assert::string($openTag);
			Assert::stringOrNull($closeTag);

			$this->openTag = $openTag;
			$this->children = $children;
			$this->closeTag = $closeTag;
		}


		public function toString()
		{
			$s = $this->openTag;

			foreach ($this->children as $child) {
				$s .= $child->toString();
			}

			return $s . $this->closeTag;
		}


		/**
		 * @return self
		 */
		public static function parse(Lexer\Stream $stream)
		{
			$openTag = $stream->consumeTokenAsText(T_OPEN_TAG, T_OPEN_TAG_WITH_ECHO);
			$children = [];
			$closeTag = NULL;
			$unknowTokens = [];

			while ($stream->hasToken()) {
				$flushUnknowTokens = TRUE;

				if ($stream->isCurrent(T_CLOSE_TAG)) {
					$closeTag = $stream->consumeTokenAsText(T_CLOSE_TAG);
					break;

				} else {
					$unknowTokens[] = $stream->consumeAnything();
					$flushUnknowTokens = FALSE;
				}

				if ($flushUnknowTokens && count($unknowTokens) > 0) {
					$children[] = new UnknowNode($unknowTokens);
					$unknowTokens = [];
				}
			}

			if (count($unknowTokens) > 0) {
				$children[] = new UnknowNode($unknowTokens);
			}

			return new self($openTag, $children, $closeTag);
		}
	}
