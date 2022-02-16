<?php
// Example #10 Static Methods
// http://www.php.net/manual/en/language.oop5.traits.php
trait StaticExample {
    public static function doSomething() {
        return 'Doing something';
    }
}

class Example {
    use StaticExample;
}

Example::doSomething();
