<?php
// Group `use` declarations
// http://php.net/manual/en/language.namespaces.importing.php
// subpath
use A\B\{A, B\C, C as D};

$a = new A;
$a = new C;
$a = new D;
