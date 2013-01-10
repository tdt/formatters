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
    public function printAll() {

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET");
        header("Expires: Sun, 19 Nov 1978 04:59:59 GMT");

        $this->printHeader();
        $this->printBody();
    }

    /**
     * This function will set the header type of the responsemessage towards the call of the user.
     */
    abstract public function printHeader();

    /**
     * This function will print the body of the responsemessage.
     */
    abstract public function printBody();
    
}