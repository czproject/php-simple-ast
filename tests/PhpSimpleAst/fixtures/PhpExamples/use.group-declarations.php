<?php
// Group `use` declarations
// http://php.net/manual/en/language.namespaces.importing.php
use some\ns\{ClassA, ClassB, ClassC as C};
use function some\ns\{fn_a, fn_b, fn_c};
use const some\ns\{ConstA, ConstB, ConstC};

$a = new ClassA; // some\ns\ClassA
$a = new ClassB; // some\ns\ClassB
$a = new ClassC; // ClassC
$a = new C; // some\ns\ClassC
$a = new fn_a; // fn_a
$a = new fn_b; // fn_b
$a = new fn_c; // fn_c
$a = new ConstA; // ConstA
$a = new ConstB; // ConstB
$a = new ConstC; // ConstC
