<?php
/**
 * This class is an abstract formatter class. It will take an object and format it to a certain format.
 * This format and the logic to format it will be implemented in a class that inherits from this class.
 *
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert   <pieter@iRail.be>
 * @author Jan Vansteenlandt <jan@iRail.be>
 */
namespace tdt\formatters;
use tdt\exceptions\TDTException;

abstract class AStrategy {
    protected $rootname;
    protected $objectToPrint;
    protected $format;

    // version of your API
    protected $version;

    /**
     * Constructor.
     * @param string $rootname Name of the rootobject, if used in the print format (i.e. xml)
     * @param Mixed  $objectToPrint Object that needs printing.
     */
    public function __construct($rootname, &$objectToPrint,$version = "1.0") {
        $this->version = $version;
        $this->rootname = $rootname;
        $this->objectToPrint = &$objectToPrint;
    }

    /**
     * This function prints the object. uses {@link printHeader()} and {@link printBody()}.
     */
    public function execute() {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET");
        header("Expires: Sun, 19 Nov 1978 04:59:59 GMT");

        $this->printHeader();

        if (!$this->isObjectAGraph())
            $this->printBody();
        else
            $this->printGraph();
    }

    /*
     * This function checks wether the object to print is an RDF graph or not
     */
    protected function isObjectAGraph() {
        foreach ($this->objectToPrint as $prop)
            return ($prop instanceof \ARC2_RDFParser);

        return false;
    }

    /**
     * This function will set the header type of the responsemessage towards the call of the user.
     */
    abstract public function printHeader();

    /**
     * This function will print the body of the responsemessage.
     */
    abstract public function printBody();

    /**
     * This function will print the body of the responsemessage when the object is a graph.
     */
    public function printGraph(){
        echo "<h1>Formatter doesn't support RDF</h1>";
        
        echo "<p>We don't have a triple output for this formatter yet. This is a best effort in HTML.</p>";
        echo "<p>There are plenty of RDF formatters which do work however. Check .ttl or .json for instance.</p>";
        $rn = $this->rootname;
        echo "<table border=3>";
        echo "<tr><td>subject</td><td>object</td><td>predicate</td></tr>";
        foreach($this->objectToPrint->$rn->triples as $triple){
            echo "<tr><td>". $triple["s"] ."</td>";
            echo "<td>". $triple["p"] ."</td>";
            echo "<td>". $triple["o"] ."</td>";

            echo "</tr>";
        }
        echo "</table>";
        throw new TDTException(453);
    }

}