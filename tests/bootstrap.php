<?php

use CzProject\PhpSimpleAst\Ast;

require __DIR__ . '/../vendor/autoload.php';

Tester\Environment::setup();


function test($caption, $cb)
{
	$cb();
}


class Tests
{
	/**
	 * @return void
	 */
	public static function equalAst(Ast\INode $expected, Ast\INode $actual)
	{
		$options = [
			\Tracy\Dumper::HASH => FALSE,
		];

		Tester\Assert::same(
			\Tracy\Dumper::toText($expected, $options),
			\Tracy\Dumper::toText($actual, $options)
		);
	}
}


class Fixtures
{
	/**
	 * @return string[]
	 */
	public static function getAll()
	{
		return self::getAllFromDirectory('');
	}


	/**
	 * @param  string $entry
	 * @return string
	 */
	public static function path($entry)
	{
		return __DIR__ . '/PhpSimpleAst/fixtures/' . $entry;
	}


	/**
	 * @param  string $entry
	 * @param  string $extension
	 * @return string
	 */
	public static function pathWithExtension($entry, $extension)
	{
		$path = self::path($entry);

		if (\Nette\Utils\Strings::endsWith($path, '.php')) {
			$path = \Nette\Utils\Strings::substring($path, 0, -4);
		}

		return $path . '.' . $extension;
	}


	/**
	 * @param  string $entry
	 * @param  string|NULL $extension
	 * @return string
	 */
	public static function load($entry, $extension = NULL)
	{
		if ($extension !== NULL) {
			return file_get_contents(self::pathWithExtension($entry, $extension));
		}

		return file_get_contents(self::path($entry));
	}


	/**
	 * @param  string $basePath
	 * @return string[]
	 */
	private static function getAllFromDirectory($basePath = '')
	{
		$directory = self::path($basePath);
		$res = [];

		foreach (scandir($directory) as $entry) {
			if ($entry === '.' || $entry === '..') {
				continue;
			}

			$entryPath = $basePath . ($basePath !== '' ? '/' : '') . $entry;
			$realPath = self::path($entryPath);

			if (is_file($realPath)) {
				if (!\Nette\Utils\Strings::endsWith($entry, '.php')) {
					continue;
				}

				$phpVersion = self::extractPhpVersion($entry);

				if ($phpVersion !== NULL && PHP_VERSION_ID < $phpVersion) {
					continue;
				}

				$res[] = $entryPath;

			} elseif (is_dir($realPath)) {
				foreach (self::getAllFromDirectory($entryPath) as $subEntryPath) {
					$res[] = $subEntryPath;
				}
			}
		}

		return $res;
	}


	/**
	 * @param  string $entryName
	 * @return int|NULL
	 */
	private static function extractPhpVersion($entryName)
	{
		$phpVersion = \Nette\Utils\Strings::before($entryName, '.');

		if (is_string($phpVersion) && \Nette\Utils\Validators::isNumericInt($phpVersion)) {
			return (int) $phpVersion;
		}

		return NULL;
	}
}
