<?php

/**
 *
 * @package tdt/formatters/strategies
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@irail.be>
 */

namespace tdt\formatters\strategies;
use \tdt\pages\Generator;

class GRAPH extends \tdt\formatters\AStrategy{

    private $m;
    private $types = array('bars', 'lines');

    /**
     * Init mustache and the formatter
     */
    public function __construct($rootname, &$objectToPrint){
        parent::__construct($rootname, $objectToPrint);

        $this->m = new \Mustache_Engine;
    }

    /**
     * Print the body
     */
    public function printBody(){
        // Check the data first
        $this->checkData();

        // Get template to render
        $template = file_get_contents(__DIR__ . "/../../../../includes/templates/graph");

        // Get flotjs
        $data['flotjs'] = file_get_contents(__DIR__ . "/../../../../includes/js/flot.min.js");


        // Set data
        $data['data'] = $this->getData();

        $values = explode(',', $_GET['values']);
        if(count($values) > 1){
            $data['title'] = "Chart for the properties '". implode(', ', $values) . "'";
        }else{
            $data['title'] = "Chart for the property '". trim($_GET['values']). "'";
        }

        // Check graph type
        $type = $_GET['type'];
        if(!(!empty($type) &&  in_array($type, $this->types))){
            $type = "lines";
        }
        $data['type'] = $type;

        $body = $this->m->render($template, $data);

        // Use the pages generator
        $generator = new Generator();
        $generator->generate($body);
    }

    /**
     * Print the header
     */
    public function printHeader(){
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: text/html; charset=UTF-8");
    }

    /**
     * Get the data provided by the request parameter values
     */
    private function getData(){

        $properties = explode(',', trim($_GET["values"]));
        $rootname = $this->rootname;
        $data = $this->objectToPrint->$rootname;
        $datastring = "";

        foreach($properties as $property){
            $property = trim($property);
            $dataset = "{label: '". $property . "' , data: [";

            $i = 0;
            // Build the data string
            foreach($data as $key => $entry){
                if(is_array($entry)){
                    if(!empty($entry[$property]) && $entry[$property] != 'null'){
                        $dataset.= "[" . $i . ", ". $entry[$property] . "],";
                    }
                }else{
                    if(!empty($entry->{$property}) && $entry->$property != 'null'){
                        $dataset.= "[" . $i . ", ". $entry->$property . "],";
                    }
                }
                $i++;
            }

            $dataset = rtrim($dataset, ",");
            $dataset .= "]}";
            $datastring .= $dataset . ", ";
        }

        // Strip last comma
        $datastring = rtrim($datastring, ",");
        return $datastring;
    }

    /**
     * Check if the values parameter has been set, if so, also check if the property exists.
     */
    private function checkData(){
        if(empty($_GET["values"])){
            echo "Pass along the field name(s) of the data you want to see displayed. This is done by using values as a request parameter (comma separated list).";
            die();
        }

        $rootname = $this->rootname;

        if(empty($this->objectToPrint->$rootname)){
            echo "The root element ".$rootname." wasn't found.";
            die();
        }
    }

    /**
     * Return some information about this formatter.
     */
    public static function getDocumentation(){
        return "This formatter returns a graph based on the field passed with the 'values' request parameter (comma separated list). It's possible to choose the type of graphy by passing a 'type' variable (default:bars|lines)";
    }
}