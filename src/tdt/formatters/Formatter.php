<?php
/**
 * This file contains the Formatter
 *
 * @copyright (C) 2011, 2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@iRail.be>
 * @author Pieter Colpaert   <pieter@iRail.be>
 */
namespace tdt\formatters;
use tdt\exceptions\TDTException;
class Formatter{

    private $format;

    public function __construct($format = ""){
        $this->setFormat($format);
    }

    /**
     * sets the requested format in the factory from the request URL
     * @param string $urlformat The format of the request i.e. json,xml,....
     */
    public function setFormat($urlformat){
        //We define the format like this:
        // * Check if $urlformat has been set
        //   - if not: probably something fishy happened, set format as error for logging purpose
        //   - else if is about: do content negotiation
        //   - else check if format exists 
        //        × throw exception when it doesn't
        //        × if it does, set $this->format with ucfirst

        //first, let's be sure about the case of the format
        $urlformat = strtoupper($urlformat);
       
        if(strtoupper($urlformat) == "about" || $urlformat == "" ){ //urlformat can be empty on SPECTQL query
            
            $cn = new \tdt\negotiatiors\ContentNegotiator();
            $format = $cn->pop();
            while(!$this->formatExists($format) && $cn->hasNext()){
                $format = $cn->pop();
                if($format == "*"){
                    $format == "XML";
                }
            }
            if(!$this->formatExists($format)){                
                throw new TDTException(451,array($format)); // could not find a suitible format
            }
            $this->format = $format;            
            //We've found our format through about, so let's set the header for content-location to the right one
            //to do this we're building our current URL and changing .about in .format
            $format= strtoupper($this->format);
            $pageURL = 'http';
            if (isset($_SERVER["HTTPS"])) {$pageURL .= "s";}
            $pageURL .= "://";
            if ($_SERVER["SERVER_PORT"] != "80") {
                $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
            } else {
                $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
            }
            $contentlocation = str_replace(".about", "." . $format, $pageURL);
            header("Content-Location:" . $contentlocation);
        }else if($this->formatExists($urlformat)){
            $this->format = $urlformat;
        }else{            
            throw new TDTException(451,array($urlformat));
        }
        
    }

    private function formatExists($format){
        return class_exists("\\tdt\\formatters\\strategies\\$format");
    }

    /*
     * This function has to create a strategy and print everything using this strategy.
     */
    private function execute($rootname, $thing){
        $format = "strategies\\" . $this->format;
        $strategy = new $format($rootname,$thing);
        $strategy->execute();
    }
    

    /**
     * Returns the format that has been set by the request
     * @return A format object
     */
    public function getFormat(){
	return $this->format;
    }


}
