<?php
// Example #12 Conflict Resolution
// http://www.php.net/manual/en/language.oop5.traits.php
trait PropertiesTrait {
    public $same = true;
    public $different = false;
}

class PropertiesExample {
    use PropertiesTrait;
    public $same = true; // Strict Standards
    public $different = true; // Fatal error
}
