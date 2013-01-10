<?php
/**
 * This file contains the Json printer.
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@iRail.be>
 * @author Pieter Colpaert   <pieter@iRail.be>
 */

namespace tdt\formatters\strategies;

class JSON extends ..\AStrategy{
     
     public function __construct($rootname,$objectToPrint){
	  parent::__construct($rootname,$objectToPrint);
     }

     public function printHeader(){
	  header("Access-Control-Allow-Origin: *");
	  header("Content-Type: application/json;charset=UTF-8");	  	  
     }

     public function printBody(){
	  if(is_object($this->objectToPrint)){
	       $hash = get_object_vars($this->objectToPrint);
	  }
          echo json_encode($hash);
     }


     public static function getDocumentation(){
         return "A javascript object notation formatter";
     }
};
?>
