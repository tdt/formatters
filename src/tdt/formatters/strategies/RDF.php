<?php
/**
 * This file contains the RDF/XML formatter.
 *
 * Includes RDF Api for PHP <http://www4.wiwiss.fu-berlin.de/bizer/rdfapi/>
 * Licensed under LGPL <http://www.gnu.org/licenses/lgpl.html>
 *
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Miel Vander Sande
 */

namespace tdt\formatters\strategies;


class RDF extends \tdt\formatters\AStrategy implements \tdt\formatters\interfaces\iSemanticFormatter{

    public function __construct($rootname, $objectToPrint) {
        parent::__construct($rootname, $objectToPrint);
    }

    public function printBody() {
        /* Serializer instantiation */
        $ser = \ARC2::getRDFXMLSerializer();
        foreach ($this->objectToPrint as $class => $prop)
            $triples = $prop->getTriples();
        /* Serialize a triples array */
        echo $ser->getSerializedTriples($triples);
    }

    public function printHeader() {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/rdf+xml; charset=UTF-8");
        header("Content-Type: text/xml; charset=UTF-8");

    }

    public static function getDocumentation(){
        return "Prints the RDF/XML notation with semantic annotations";
    }

}