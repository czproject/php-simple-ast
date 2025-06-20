<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;
	use CzProject\PhpSimpleAst\Lexer\PhpToken;


	class Modifiers
	{
		/** @var string */
		private $indentation;

		/** @var list<IConstantModifier>|list<IPropertyModifier>|list<IMethodModifier> */
		private $modifiers;


		/**
		 * @param list<IConstantModifier>|list<IPropertyModifier>|list<IMethodModifier> $modifiers
		 */
		private function __construct(string $indentation, array $modifiers)
		{
			$this->indentation = $indentation;
			$this->modifiers = $modifiers;
		}


		public function getIndentation(): string
		{
			return $this->indentation;
		}


		/**
		 * @return IConstantModifier[]
		 */
		public function toConstantModifiers()
		{
			foreach ($this->modifiers as $modifier) {
				Assert::type($modifier, IConstantModifier::class, 'Modifier ' . get_class($modifier) . ' is not ' . IConstantModifier::class);
			}

			return $this->modifiers;
		}


		/**
		 * @return IMethodModifier[]
		 */
		public function toMethodModifiers()
		{
			foreach ($this->modifiers as $modifier) {
				Assert::type($modifier, IMethodModifier::class, 'Modifier ' . get_class($modifier) . ' is not ' . IMethodModifier::class);
			}

			return $this->modifiers;
		}


		/**
		 * @return IPropertyModifier[]
		 */
		public function toPropertyModifiers()
		{
			foreach ($this->modifiers as $modifier) {
				Assert::type($modifier, IPropertyModifier::class, 'Modifier ' . get_class($modifier) . ' is not ' . IPropertyModifier::class);
			}

			return $this->modifiers;
		}


		public static function empty(string $indentation): self
		{
			return new self($indentation, []);
		}


		public static function parse(NodeParser $parser): self
		{
			$nodeIndentation = $parser->consumeNodeIndentation();
			$modifiers = [];

			do {
				if ($parser->isCurrent(T_PUBLIC, T_PROTECTED, T_PRIVATE)) {
					$modifiers[] = Visibility::parse($parser->createSubParser());

				} elseif ($parser->isCurrent(T_STATIC)) {
					$modifiers[] = StaticModifier::parse($parser->createSubParser());

				} elseif ($parser->isCurrent(T_ABSTRACT, T_FINAL)) {
					$modifiers[] = OverridingModifier::parse($parser->createSubParser());

				} elseif ($parser->isCurrent(PhpToken::T_READONLY())) {
					$modifiers[] = ReadOnlyModifier::parse($parser->createSubParser());

				} else {
					$parser->errorUnknowToken();
				}

				$parser->tryConsumeWhitespace();

			} while ($parser->isCurrent(T_PUBLIC, T_PROTECTED, T_PRIVATE, T_STATIC, T_ABSTRACT, T_FINAL, PhpToken::T_READONLY()));

			$parser->close();
			return new self($nodeIndentation, $modifiers);
		}
	}
