<?php

require "../vendor/autoinclude.php";
//Using formatters is easy!


// With an stdClass
$a = new stdClass();
$a->b = "c";
// the constructor takes 1 argument:
//                   The format strategy. If left empty, the content negotiator will be enabled
$f = new \tdt\formatters\Formatter("CSV");
// The formatter will choose a strategy based on the the format strategy and based on the input.

// On execution, the formatter will detect $a is a stdClass and will format the output accordingly.
$f->execute($a);
