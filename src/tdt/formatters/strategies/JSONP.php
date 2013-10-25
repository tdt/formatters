<?php
/**
 * This file contains the Jsonp printer.
 * @package The-Datatank/formatters
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@iRail.be>
 * @author Pieter Colpaert   <pieter@iRail.be>
 */

namespace tdt\formatters\strategies;
use tdt\exceptions\TDTException;

class JSONP extends \tdt\formatters\strategies\JSON{

	private $callback;

	public function __construct($rootname,$objectToPrint,$callback = ""){

		parent::__construct($rootname,$objectToPrint);
		if(empty($_GET['callback'])){
            echo "Provide the callback parameter in the request URI.";
			header('HTTP/1.0 400 Bad call', true, 400);
            die;
		}

	}

	public function printHeader(){
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/javascript;charset=UTF-8");
	}

	public function printBody(){
		echo  $_GET['callback'] . '(';
       	parent::printBody();
        $json = ob_get_contents();
		echo ')';
	}


public static function getDocumentation(){
	return "Prints json but will wrap the output in the callback function specified";
}
}
