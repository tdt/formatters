<?php

/**
 * This file contains the Json printer.
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@iRail.be>
 * @author Pieter Colpaert   <pieter@iRail.be>
 */

namespace tdt\formatters\strategies;

class JSON extends \tdt\formatters\AStrategy {

    public function __construct($rootname, $objectToPrint) {
        parent::__construct($rootname, $objectToPrint);
    }

    public function printHeader() {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json;charset=UTF-8");
    }

    public function printBody() {
        if (is_object($this->objectToPrint)) {
            $hash = get_object_vars($this->objectToPrint);
        }
        echo str_replace("\/","/",json_encode($hash));
    }

    public function printGraph() {
        /* Serializer instantiation */
        $ser = \ARC2::getRDFJSONSerializer();
        foreach ($this->objectToPrint as $class => $prop)
            $triples = $prop->getTriples();
        /* Serialize a triples array */
        echo $ser->getSerializedTriples($triples);
    }

    public static function getDocumentation() {
        return "A javascript object notation formatter";
    }

}
