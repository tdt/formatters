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

class JSONP extends ..\AStrategy{

     private $callback;

     public function __construct($rootname,$objectToPrint,$callback = ""){
	  if($callback != ""){
	       $this->callback = $callback;
	  }else{
	       throw new \tdt\framework\TDTException(452, array("With Jsonp you should add a callback: &callback=yourfunctionname"));
	  }
	  parent::__construct($rootname,$objectToPrint);
     }

     public function printHeader(){
	  header("Access-Control-Allow-Origin: *");
	  header("Content-Type: application/json;charset=UTF-8");	  
     }

     public function printBody(){
	  echo $this->callback . '(';
	  parent::printBody();
	  echo ')';
     }


    public static function getDocumentation(){
        return "Prints json but will wrap the output in the callback function specified";
    }
};
?>
