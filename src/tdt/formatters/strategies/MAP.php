<?php

/**
 * @package tdt/formatters/strategies
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@irail.be>
 * @author Michiel Vancoillie <michiel@irail.be>
 */

namespace tdt\formatters\strategies;

define('INC', __DIR__ . '/../../../../includes/');

class MAP extends \tdt\formatters\AStrategy {

    public function __construct($rootname, $objectToPrint) {
        parent::__construct($rootname, $objectToPrint);
    }

    public function printHeader() {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: text/html; charset=UTF-8");
    }

    public function printBody() {
        // Parse a kml from the objectToPrint, and convert it to a geojson.
        ob_start();
        $formatter = new \tdt\formatters\strategies\KML($this->rootname, $this->objectToPrint);
        $formatter->printBody();
        $kml = ob_get_contents();
        ob_end_clean();
    ?>
        <!DOCTYPE html>
        <html>
            <head>
                <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
                <script src="//cdn.leafletjs.com/leaflet-0.5/leaflet.js"></script>
                <script type="text/javascript">
                    <?php
                        // KML Leaflet plugin
                        include(INC."js/leaflet.KML.js");
                    ?>
                </script>
                <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.5/leaflet.css" />
                <style>
                    body { margin:0; padding:0; }
                    #map { position:absolute; top:0; bottom:0; width:100%; }
                </style>
            </head>
            <body>
                <div id='map'></div>
                <script>
                    var map = L.map('map').setView([51,3], 7);
                    L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors</a>',
                        maxZoom: 18
                    }).addTo(map);

                    var track = new L.KML('<?= preg_replace("/'/", "\\'", $kml) ?>');
                    var data = new L.FeatureGroup();
                    for(i in track._layers){
                        data.addLayer(track._layers[i]);
                    }
                    data.addTo(map);
                    map.fitBounds(data.getBounds());
                </script>
            </body>
        </html>
    <?php
    }

    /**
     * Return some information about what this formatter does.
     */
    public static function getDocumentation(){
        return "The Osm formatter is a formatter that generates a map visualisation.";
    }
}