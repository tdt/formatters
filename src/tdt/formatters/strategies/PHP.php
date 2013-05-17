<?php
/**
 * This file contains the php formatter.
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@iRail.be>
 * @author Pieter Colpaert   <pieter@iRail.be>
 */

namespace tdt\formatters\strategies;

class PHP extends \tdt\formatters\AStrategy{

   public function __construct($rootname,$objectToPrint){
        parent::__construct($rootname,$objectToPrint);
    }

    public function printHeader(){
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: text/plain;charset=UTF-8");
    }

    public function printBody(){
        $this->serializeObject();
    }

    public function printGraph(){
        $this->serializeObject();
    }

    /**
     * Echoes the object to print in a php serialization
     */
    private function serializeObject(){
        if(is_object($this->objectToPrint)){
            $hash = get_object_vars($this->objectToPrint);
        }
        $hash['version'] = $this->version;
        $hash['timestamp'] = time();
        echo serialize($hash);
    }

    public static function getDocumentation(){
        return "Prints php object notation. This can come in handy for php serialization";
    }

}