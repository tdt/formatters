<?php
/**
 * The Kml-formatter is a formatter which will search for location objects throughout the documenttree and return a file with placemarks
 *
 * @copyright (C) 2011, 2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@iRail.be>
 * @author Pieter Colpaert   <pieter@iRail.be>
 */

namespace tdt\formatters\strategies;

class KML extends \tdt\formatters\AStrategy{

    public function __construct($rootname,$objectToPrint){
        parent::__construct($rootname,$objectToPrint);
    }

    public function printHeader(){
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/vnd.google-earth.kml+xml; charset=utf-8");
    }

    public function printBody(){

        /*
         * print the KML header first
         */
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
        echo "<kml xmlns=\"http://www.opengis.net/kml/2.2\">";
        /*
         * Second step is to check every locatable object and print it
         */
        echo "<Document>";

        $this->printPlacemarks($this->objectToPrint);
        echo "</Document>";

        echo "</kml>";
    }

    private function printPlacemarks($val){
        $hash = get_object_vars($val);
        $this->printArray($hash);
    }

    private function xmlgetelement($value){
        $result = "<![CDATA[";
        // Need to discuss what happens with this, the data create by the DCATA element is not used in our current map formatter

        $result .= "]]>";
        return $result;
    }

    private function getExtendedDataElement($value){
        $result = "<ExtendedData>";
        // Need to discuss what happens with this, the data create by the DCATA element is not used in our current map formatter
        $result .= "</ExtendedData>";
        return $result;
    }

    private function printArray(&$val){

        foreach($val as $key => &$value) {
            $long = "";
            $lat = "";
            $coords = array();
            if(is_array($value)) {
                $array = $value;
            }
            if (is_object($value)) {
                $array = get_object_vars($value);
            }
            if(isset($array)) {
                $longkey = $this->array_key_exists_nc("long",$array);
                if (!$longkey) {
                    $longkey = $this->array_key_exists_nc("longitude",$array);
                }
                $latkey = $this->array_key_exists_nc("lat",$array);
                if (!$latkey) {
                    $latkey = $this->array_key_exists_nc("latitude",$array);
                }
                $coordskey = $this->array_key_exists_nc("coords",$array);
                if (!$coordskey) {
                    $coordskey = $this->array_key_exists_nc("coordinates",$array);
                }
                if($longkey && $latkey) {
                    $long = $array[$longkey];
                    $lat = $array[$latkey];
                    unset($array[$longkey]);
                    unset($array[$latkey]);
                    $name = $this->xmlgetelement($array);
                    $extendeddata = $this->getExtendedDataElement($array);
                } else if($coordskey) {
                    $coords = explode(";",$array[$coordskey]);
                    unset($array[$coordskey]);
                    $name = $this->xmlgetelement($array);
                    $extendeddata = $this->getExtendedDataElement($array);
                }
                else {
                    $this->printArray($array);
                }
                if(($lat != "" && $long != "") || count($coords) != 0){
                    echo "<Placemark><name>". htmlspecialchars($key) ."</name><Description>".$name."</Description>";
                    echo $extendeddata;
                    if($lat != "" && $long != "") {
                        echo "<Point><coordinates>".$long.",".$lat."</coordinates></Point>";
                    }
                    if (count($coords)  > 0) {
                        if (count($coords)  == 1) {
                            echo "<Polygon><outerBoundaryIs><LinearRing><coordinates>".$coords[0]."</coordinates></LinearRing></outerBoundaryIs></Polygon>";
                        } else {
                            echo "<MultiGeometry>";
                            foreach($coords as $coord) {
                                echo "<LineString><coordinates>".$coord."</coordinates></LineString>";
                            }
                            echo "</MultiGeometry>";
                        }
                    }
                    echo "</Placemark>";
                }
            }
        }
    }

    /**
     * Case insensitive version of array_key_exists.
     * Returns the matching key on success, else false.
     *
     * @param string $key
     * @param array $search
     * @return string|false
     */
    private function array_key_exists_nc($key, $search) {
        if (array_key_exists($key, $search)) {
            return $key;
        }
        if (!(is_string($key) && is_array($search) && count($search))) {
            return false;
        }
        $key = strtolower($key);
        foreach ($search as $k => $v) {
            if (strtolower($k) == $key) {
                return $k;
            }
        }
        return false;
    }

    public function printGraph(){

        $nameuris = array(
            "http://schema.org/name"
        );

        $longitudeuris = array(
            "http://www.w3.org/2003/01/geo/wgs84_pos#lon"
        );

        $latitudeuris = array(
            "http://www.w3.org/2003/01/geo/wgs84_pos#lat"
        );

        $geometryuris = array(
            "http://www.w3.org/2003/01/geo/wgs84_pos#geometry"
        );

        $rn = $this->rootname;
        $new = new \stdClass();
        foreach($this->objectToPrint->$rn->triples as $t){
            if(in_array($t["p"],$nameuris)){
                if(!isset($new->$t["s"])){
                    $new->$t["s"] = array();
                }
                $s = &$new->$t["s"];
                $s["name"] = $t["o"];
            }
            if(in_array($t["p"],$longitudeuris)){
                if(!isset($new->$t["s"])){
                    $new->$t["s"] = array();
                }
                $s = &$new->$t["s"];
                $s["longitude"] = $t["o"];
            }
            if(in_array($t["p"],$latitudeuris)){
                if(!isset($new->$t["s"])){
                    $new->$t["s"] = array();
                }
                $s = &$new->$t["s"];
                $s["latitude"] = $t["o"];
            }

            if(in_array($t["p"],$geometryuris)){
                if(empty($new->$t["s"])){
                    $new->$t["s"] = array();
                }
                $s = &$new->$t["s"];
                // Geometry holds a sequence of coordinates, each couple of coordinates needs to be put as an entry into the resulting array.
                // The string that holds the geometry is build: MULTISTRING (( ... ..., ... ...), ... ...))
                // Look at the kml printBody formatter above.
                $coords = "";
                $coord_string = substr($t["o"],17,-1);
                $line_strings = explode("),",$coord_string);
                foreach($line_strings as $line_string){
                    $couples = explode(",",$line_string);
                    $coord_string = "";
                    foreach($couples as $couple){
                        $couple = trim($couple);
                        $couple = rtrim($couple,')');
                        $couple = ltrim($couple,'(');
                        $couple = preg_replace('/\s+/', ',', $couple);
                        $coord_string .= $couple . " ";
                    }
                    $coords .= $coord_string;
                    $coords .= ";";
                }
                $s["coords"] = rtrim($coords,";");
            }
        }
        $this->objectToPrint->$rn = $new;
        $this->printBody();
    }

    public static function getDocumentation(){
        return "Will try to find locations in the entire object and print them as KML points";
    }
}
