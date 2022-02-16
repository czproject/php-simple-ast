<?php
// Group `use` declarations
// http://php.net/manual/en/language.namespaces.importing.php
// spaces
use some\ns\{ ClassA, ClassB, ClassC as C };

$a = new ClassA; // some\ns\ClassA
$a = new ClassB; // some\ns\ClassB
$a = new ClassC; // ClassC
$a = new C; // some\ns\ClassC
