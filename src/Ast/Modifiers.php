<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class Modifiers
	{
		/** @var string */
		private $indentation;

		/** @var array<Visibility|OverridingModifier|StaticModifier> */
		private $modifiers;


		/**
		 * @param string $indentation
		 * @param array<Visibility|OverridingModifier|StaticModifier> $modifiers
		 */
		private function __construct($indentation, array $modifiers)
		{
			Assert::string($indentation);

			$this->indentation = $indentation;
			$this->modifiers = $modifiers;
		}


		/**
		 * @return string
		 */
		public function getIndentation()
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


		/**
		 * @param  string $indentation
		 * @return self
		 */
		public static function empty($indentation)
		{
			return new self($indentation, []);
		}


		/**
		 * @return self
		 */
		public static function parse(NodeParser $parser)
		{
			$modifiers = [];

			do {
				if ($parser->isCurrent(T_PUBLIC, T_PROTECTED, T_PRIVATE)) {
					$modifiers[] = Visibility::parse($parser->createSubParser());

				} elseif ($parser->isCurrent(T_STATIC)) {
					$modifiers[] = StaticModifier::parse($parser->createSubParser());

				} elseif ($parser->isCurrent(T_ABSTRACT, T_FINAL)) {
					$modifiers[] = OverridingModifier::parse($parser->createSubParser());

				} else {
					$parser->errorUnknowToken();
				}

				$parser->tryConsumeWhitespace();

			} while ($parser->isCurrent(T_PUBLIC, T_PROTECTED, T_PRIVATE, T_STATIC, T_ABSTRACT, T_FINAL));

			$parser->close();
			return new self($parser->getNodeIndentation(), $modifiers);
		}
	}
