<?php

require __DIR__ . '/../vendor/autoload.php';

Tester\Environment::setup();


function test($caption, $cb)
{
	$cb();
}


class Fixtures
{
	/**
	 * @return string[]
	 */
	public static function getAll()
	{
		$res = [];

		foreach (scandir(__DIR__ . '/PhpSimpleAst/fixtures/') as $entry) {
			if ($entry === '.' || $entry === '..') {
				continue;
			}

			if (is_file(self::path($entry))) {
				$res[] = $entry;
			}
		}

		return $res;
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
	 * @return string
	 */
	public static function load($entry)
	{
		return file_get_contents(self::path($entry));
	}
}
