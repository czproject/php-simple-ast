<?php

	declare(strict_types=1);

	namespace CzProject\PhpSimpleAst\Refactor;

	use CzProject\PhpSimpleAst\Reflection;
	use Nette\Utils\Strings;


	class PhpDocParamFixer
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
				$docComment = $methodReflection->getDocComment();

				if ($docComment === NULL) {
					continue;
				}

				$index = 0;
				$newDocComment = Strings::replace($docComment, "#(\\s*\\*\\s*@param\\s+)([^@\\n\\r]*)#", function (array $m) use (&$index, $methodReflection) {
					$tag = $m[1];
					$value = $m[2];
					$tokens = preg_split('/(\s+)/', $value, 2, \PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

					if ($tokens === FALSE || count($tokens) === 0) {
						return $m[0];
					}

					if (isset($tokens[2]) && Strings::startsWith($tokens[2], '$')) {
						$index++;
						return $m[0];
					}

					if ($methodReflection->hasParameterByIndex($index)) {
						$parameterReflection = $methodReflection->getParameterByIndex($index);
						$tokens[0] .= ' $' . $parameterReflection->getName();

					} else {
						$index++;
						return '';
					}

					$index++;
					return $tag . implode('', $tokens);
				});

				if ($newDocComment !== $docComment) {
					$methodReflection->setDocComment($newDocComment);
				}
			}
		}
	}
