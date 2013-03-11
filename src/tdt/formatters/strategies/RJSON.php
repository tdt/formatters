<?php
/**
 * This file contains the RDF/JSON formatter.
 *
 * Includes RDF Api for PHP <http://www4.wiwiss.fu-berlin.de/bizer/rdfapi/>
 * Licensed under LGPL <http://www.gnu.org/licenses/lgpl.html>
 *
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Miel Vander Sande
 */

namespace tdt\formatters\strategies;

class RJSON extends \tdt\formatters\AStrategy implements \tdt\formatters\interfaces\iSemanticFormatter {

    public function __construct($rootname, $objectToPrint) {
        parent::__construct($rootname, $objectToPrint);
    }

    public function printBody() {
        $triples = $this->objectToPrint->getTriples();
        echo $this->objectToPrint->toRDFJSON($triples);
    }

    public function printHeader() {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json;charset=UTF-8");
    }

    public static function getDocumentation(){
        return "Prints in the Talis RDF JSON notation with semantic annotations";
    }

}
