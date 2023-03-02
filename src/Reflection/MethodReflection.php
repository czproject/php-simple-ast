<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Reflection;

	use CzProject\Assert\Assert;
	use CzProject\PhpSimpleAst\Ast;


	class MethodReflection
	{
		/** @var string */
		private $declaringClassName;

		/** @var Ast\MethodNode */
		private $node;

		/** @var array<string, ParameterReflection>|NULL */
		private $parameters = NULL;

		/** @var array<int, ParameterReflection>|NULL */
		private $parametersByIndex = NULL;


		private function __construct(
			string $declaringClassName,
			Ast\MethodNode $node
		)
		{
			$this->declaringClassName = $declaringClassName;
			$this->node = $node;
		}


		public function getDeclaringClassName(): string
		{
			return $this->declaringClassName;
		}


		public function getName(): string
		{
			return $this->node->getName();
		}


		public function getFullName(): string
		{
			return $this->declaringClassName . '::' . $this->node->getName();
		}


		public function getDocComment(): ?string
		{
			return $this->node->getDocComment();
		}


		public function setDocComment(string $docComment): void
		{
			$this->node->setDocComment($docComment);
		}


		/**
		 * @return array<string, ParameterReflection>
		 */
		public function getParameters(): array
		{
			if ($this->parameters === NULL) {
				$this->parameters = [];
				$this->parametersByIndex = [];

				foreach ($this->node->getParameters() as $index => $parameter) {
					$parameterReflection = new ParameterReflection($index, $parameter);
					$this->parameters[$parameterReflection->getName()] = $parameterReflection;
					$this->parametersByIndex[$index] = $parameterReflection;
				}
			}

			return $this->parameters;
		}


		/**
		 * @return array<int, ParameterReflection>
		 */
		public function getIndexedParameters(): array
		{
			$this->getParameters();
			Assert::true($this->parametersByIndex !== NULL);
			return $this->parametersByIndex;
		}


		public function getParameter(string $name): ParameterReflection
		{
			$parameters = $this->getParameters();
			Assert::true(isset($parameters[$name]), 'Parameter ' . $name . ' not found.');
			return $parameters[$name];
		}


		public function hasParameterByIndex(int $index): bool
		{
			$this->getParameters();
			return isset($this->parametersByIndex[$index]);
		}


		public function getParameterByIndex(int $index): ParameterReflection
		{
			$this->getParameters();
			Assert::true(isset($this->parametersByIndex[$index]), 'Parameter #' . $index . ' not found.');
			return $this->parametersByIndex[$index];
		}


		public function hasReturnType(): bool
		{
			return $this->node->hasReturnType();
		}


		public function isPublic(): bool
		{
			return $this->node->isPublic();
		}


		public function isProtected(): bool
		{
			return $this->node->isProtected();
		}


		public function isPrivate(): bool
		{
			return $this->node->isPrivate();
		}


		public function setVisibilityToPublic(): void
		{
			$this->node->setVisibilityToPublic();
		}


		public function setVisibilityToProtected(): void
		{
			$this->node->setVisibilityToProtected();
		}


		public function setVisibilityToPrivate(): void
		{
			$this->node->setVisibilityToPrivate();
		}


		/**
		 * @return self[]
		 */
		public static function createFromClass(string $declaringClassName, Ast\ClassNode $classNode): array
		{
			$result = [];

			foreach (Ast\Tree::find($classNode, Ast\MethodNode::class) as $tree) {
				$result[] = new self($declaringClassName, $tree->getNode());
			}

			return $result;
		}
	}
