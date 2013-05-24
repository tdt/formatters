<?php

require "../vendor/autoload.php";
use \tdt\formatters\Formatter;

// Test the bar formatter by adding "?values=cash,stocks" to the URI

// Create data
$data = new stdClass();
$data->chart = array();

for($i = 0; $i<20; $i++){
    $item = new stdClass();
    $item->cash = round(rand(0, 1000));
    $item->stocks = round(rand(0, 1000));
    array_push($data->chart, $item);
}

// Pass the format strategy as argument, when left empty, the content negotiator will be enabled
$f = new Formatter("GRAPH");
// The formatter will choose a strategy based on the the format strategy and based on the input.

// On execution, the formatter will detect $a is a stdClass and will format the output accordingly.
$f->execute("chart", $data);