<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Reflection;

	use CzProject\Assert\Assert;
	use CzProject\PhpSimpleAst\Ast;


	class ParameterReflection
	{
		/** @var int */
		private $index;

		/** @var Ast\Parameter */
		private $parameter;


		public function __construct(
			int $index,
			Ast\Parameter $parameter
		)
		{
			Assert::true($index >= 0, 'Index must be zero or positive-int.');

			$this->index = $index;
			$this->parameter = $parameter;
		}


		public function getIndex(): int
		{
			return $this->index;
		}


		public function getName(): string
		{
			return $this->parameter->getName();
		}


		public function isPassedByReference(): bool
		{
			return $this->parameter->isPassedByReference();
		}


		public function getType(): ?Ast\Type
		{
			return $this->parameter->getType();
		}


		public function getDefaultValue(): ?Ast\DefaultValue
		{
			return $this->parameter->getDefaultValue();
		}


		public function isNullable(): bool
		{
			$type = $this->parameter->getType();

			if ($type === NULL) {
				return FALSE;
			}

			return $type->isNullable();
		}


		public function setNullable(bool $nullable): void
		{
			$type = $this->parameter->getType();

			if ($type === NULL) {
				throw new \CzProject\PhpSimpleAst\InvalidStateException('Parameter has no type.');
			}

			$type->setNullable($nullable);
		}
	}
