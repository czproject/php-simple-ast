<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Ast;

	use CzProject\Assert\Assert;


	class Visibility implements IMethodFlag, IPropertyFlag
	{
		/** @var string */
		private $indentation;

		/** @var string */
		private $visibility;


		/**
		 * @param string $indentation
		 * @param string $visibility
		 */
		public function __construct($indentation, $visibility)
		{
			Assert::string($indentation);
			Assert::string($visibility);

			$lowerValue = strtolower($visibility);
			Assert::true($lowerValue === 'public' || $lowerValue === 'protected' || $lowerValue === 'private', 'Invalid visibility');

			$this->indentation = $indentation;
			$this->visibility = $visibility;
		}


		/**
		 * @return bool
		 */
		public function isPublic()
		{
			return strtolower($this->visibility) === 'public';
		}


		/**
		 * @return bool
		 */
		public function isProtected()
		{
			return strtolower($this->visibility) === 'protected';
		}


		/**
		 * @return bool
		 */
		public function isPrivate()
		{
			return strtolower($this->visibility) === 'private';
		}


		/**
		 * @return void
		 */
		public function setAsPublic()
		{
			$this->visibility = 'public';
		}


		/**
		 * @return void
		 */
		public function setAsProtected()
		{
			$this->visibility = 'protected';
		}


		/**
		 * @return void
		 */
		public function setAsPrivate()
		{
			$this->visibility = 'private';
		}


		public function toString()
		{
			return $this->indentation . $this->visibility;
		}


		/**
		 * @return self
		 */
		public static function parse(NodeParser $parser)
		{
			$visibility = $parser->consumeTokenAsText(T_PUBLIC, T_PROTECTED, T_PRIVATE);
			$parser->close();
			return new self($parser->getNodeIndentation(), $visibility);
		}
	}
