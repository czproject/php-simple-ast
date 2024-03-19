<?php

class Foo {
	public static function getInstance(): static {
		return new static();
	}
}
