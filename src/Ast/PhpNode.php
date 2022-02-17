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
			$closeTag = NULL;
			$buffer = new NodeBuffer($stream);

			while ($stream->hasToken()) {
				$child = NULL;

				if ($stream->isCurrent(T_CLOSE_TAG)) {
					$closeTag = $stream->consumeTokenAsText(T_CLOSE_TAG);
					break;

				} elseif ($stream->isCurrent(T_NAMESPACE)) {
					$child = NamespaceNode::parse($buffer->flushIndentation(), $stream);
				}

				$buffer->onChild($child);
			}

			$buffer->close();
			return new self($openTag, $buffer->getChildren(), $closeTag);
		}
	}
