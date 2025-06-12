<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Refactor;

	use CzProject\PhpSimpleAst\Reflection;


	class NullableParameterFixer
	{
		/**
		 * @param  Reflection\ClassReflection[] $classes
		 */
		public static function processClasses(array $classes): void
		{
			foreach ($classes as $class) {
				self::processClass($class);
			}
		}


		public static function processClass(Reflection\ClassReflection $classReflection): void
		{
			foreach ($classReflection->getMethods() as $methodReflection) {
				$parameters = $methodReflection->getParameters();

				foreach ($parameters as $parameter) {
					$type = $parameter->getType();
					$defaultValue = $parameter->getDefaultValue();

					if ($type !== NULL
						&& $defaultValue !== NULL
						&& $defaultValue->isNull()
						&& $type->isSingle()
						&& !$type->isNullable()
					) {
						$parameter->setNullable(true);
					}
				}
			}
		}
	}
