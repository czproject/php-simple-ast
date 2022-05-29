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
	}
