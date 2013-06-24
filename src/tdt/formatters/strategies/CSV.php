<?php
/**
 * This file contains the CSV printer.
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert   <pieter@iRail.be>
 * @author Jan Vansteenlandt <jan@iRail.be>
 */

namespace tdt\formatters\strategies;
use tdt\exceptions\TDTException;

class CSV extends \tdt\formatters\AStrategy{

    public function printHeader(){
       header("Access-Control-Allow-Origin: *");
       header("Content-Type: text/csv;charset=UTF-8");
    }

    /**
     * Encloses the $element in double quotes.
     */
    private function enclose($element){
        $element = rtrim($element, '"');
        $element = ltrim($element, '"');
        $element = '"'.$element.'"';
        return utf8_encode($element);
    }

    public function printBody(){

        $keys = array_keys(get_object_vars($this->objectToPrint));
        $key = $keys[0];
        $this->objectToPrint = $this->objectToPrint->$key;

        if(!is_array($this->objectToPrint)){
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(452, array("You can only request a CSV formatter on a tabular datastructure."), $exception_config);
        }


        $header_printed = false;
        foreach($this->objectToPrint as $row){
            if(is_object($row)){
               $row = get_object_vars($row);
            }else if(!is_array($row)){
                echo $row . "\n";
                continue;
            }

            if(!$header_printed){
                $i = 0;
                foreach($row as $key => $value){
                    echo $this->enclose($key);
                    echo sizeof($row)-1 != $i ? ";" : "\n";
                    $i++;
                }
                $header_printed = true;
            }

            $i = 0;
            foreach($row as $element){

                if(is_object($element)){
                    if(isset($element->id)){
                        echo $element->id;
                    }else if(isset($element->name)){
                        echo $element->name;
                    }else{
                        echo "OBJECT";
                    }
                }
                elseif(is_array($element)){
                    if(isset($element["id"])){
                        echo $element["id"];
                    }else if(isset($element["name"])){
                        echo $element["name"];
                    }else{
                        echo "OBJECT";
                    }
                }
                else{
                    echo $this->enclose($element);
                }
                echo sizeof($row)-1 != $i ? ";" : "\n";
                $i++;
            }
        }
    }

    public static function getDocumentation(){
        return "A CSV formatter. Works only on tabular data.";
    }
}

