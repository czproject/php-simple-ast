<?php
// Example #11 Defining Properties
// http://www.php.net/manual/en/language.oop5.traits.php
trait PropertiesTrait {
    public $x = 1;
}

class PropertiesExample {
    use PropertiesTrait;
}

$example = new PropertiesExample;
$example->x;
