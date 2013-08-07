<?php

/**
 * The Html formatter formats everything for development purpose
 *
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@iRail.be>
 * @author Pieter Colpaert   <pieter@iRail.be>
 * @author Miel Vander Sande
 */

namespace tdt\formatters\strategies;

use tdt\pages\Generator;

class HTML extends \tdt\formatters\AStrategy {

    private $thead;
    private $tbody;
    private $rowcontents;
    private $headcontents;

    public function __construct($rootname, $objectToPrint) {
        parent::__construct($rootname, $objectToPrint);
    }

    public function printHeader() {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: text/html; charset=UTF-8");
    }

    public function printBody() {
        $generator = new Generator();

        $output = $this->displayTree($this->objectToPrint);

        $h = headers_list();
        $i = 0;
        $matches = array();
        while($i < sizeof($h) && !preg_match( "/Link: (.+);rel=next.*/" , $h[$i], $matches)){
            $i++;
        }
        if($i < sizeof($h)){
            //$output .= "<p class='nextpage'><a href='". $matches[1] ."'>Next page</a></p>";
        }
        $generator->generate($output, 'resource');
    }

    public static function getDocumentation(){
        return "The HTML formatter is a formatter which prints nice output for users. It prints everything in the internal object and extra links towards meta-data and documentation.";
    }

    private function getUrl($type) {
        $ext = explode(".", $_SERVER['REQUEST_URI']);
        return "http://" . $_SERVER['HTTP_HOST'] . str_replace('.' . $ext[1],'.' . $type,$_SERVER['REQUEST_URI']);
    }

    private function displayTree($var) {
        if (is_object($this->objectToPrint)) {
            $hash = get_object_vars($this->objectToPrint);
        }

        $formattedJSON = $this->prettyPrint(json_encode($hash));

        return str_replace("\/","/", $formattedJSON);
    }

    private function prettyPrint($json){
        $result = '';
        $level = 0;
        $prev_char = '';
        $in_quotes = false;
        $ends_line_level = NULL;
        $json_length = strlen( $json );

        for( $i = 0; $i < $json_length; $i++ ) {
            $char = $json[$i];
            $new_line_level = NULL;
            $post = "";
            if( $ends_line_level !== NULL ) {
                $new_line_level = $ends_line_level;
                $ends_line_level = NULL;
            }
            if( $char === '"' && $prev_char != '\\' ) {
                $in_quotes = !$in_quotes;
            } else if( ! $in_quotes ) {
                switch( $char ) {
                    case '}': case ']':
                        $level--;
                        $ends_line_level = NULL;
                        $new_line_level = $level;
                        break;

                    case '{': case '[':
                        $level++;
                    case ',':
                        $ends_line_level = $level;
                        break;

                    case ':':
                        $post = " ";
                        break;

                    case " ": case "\t": case "\n": case "\r":
                        $char = "";
                        $ends_line_level = $new_line_level;
                        $new_line_level = NULL;
                        break;
                }
            }
            if( $new_line_level !== NULL ) {
                $result .= "\n".str_repeat( "    ", $new_line_level );
            }
            $result .= $char.$post;
            $prev_char = $char;
        }

        return $result;
    }
}