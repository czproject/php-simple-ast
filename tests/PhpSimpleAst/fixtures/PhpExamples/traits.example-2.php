<?php
// Example #2 Precedence Order Example
// http://www.php.net/manual/en/language.oop5.traits.php
class Base {
    public function sayHello() {
        echo 'Hello ';
    }
}

trait SayWorld {
    public function sayHello() {
        parent::sayHello();
        echo 'World!';
    }
}

class MyHelloWorld extends Base {
    use SayWorld;
}

$oh = new MyHelloWorld();
$oh->sayHello();
