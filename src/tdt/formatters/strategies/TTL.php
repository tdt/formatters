<?php

/**
 * This file contains the RDF/Turtle formatter.
 *
 * Includes RDF Api for PHP <http://www4.wiwiss.fu-berlin.de/bizer/rdfapi/>
 * Licensed under LGPL <http://www.gnu.org/licenses/lgpl.html>
 *
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Miel Vander Sande
 */

namespace tdt\formatters\strategies;

class TTL extends \tdt\formatters\AStrategy implements \tdt\formatters\interfaces\iSemanticFormatter {

    public function __construct($rootname, $objectToPrint) {
        parent::__construct($rootname, $objectToPrint);
    }

    public function printBody() {
        /* Serializer instantiation */
        $ser = \ARC2::getTurtleSerializer();
        foreach ($this->objectToPrint as $class => $prop)
            $triples = $prop->getTriples();
        /* Serialize a triples array */
        echo $ser->getSerializedTriples($triples);
    }

    public function printHeader() {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: text/turtle; charset=UTF-8");
    }

    public static function getDocumentation() {
        return "Prints the Turtle notation with semantic annotations";
    }

}
