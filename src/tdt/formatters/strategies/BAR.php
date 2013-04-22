<?php

namespace tdt\formatters\strategies;

class BAR extends \tdt\formatters\AStrategy{

    public function __construct($rootname, &$objectToPrint){
        parent::__construct($rootname,$objectToPrint);
    }

    public function printBody(){
?>
    <!DOCTYPE html>
<html>
  <head>
    <title>Barchart</title>
    <script type="text/javascript" src="http://d3js.org/d3.v3.min.js"></script>
  </head>
  <body>

    <?php
        $this->checkProperty();
    ?>

    <script type="text/javascript">

        var dataset = [<?php echo $this->getData(); ?>];
        var w = 1000;
        var h = 200;
        var fontsize = 12;
        var titleFontSize = 16;
        var labelPadding = 30;
        var barPadding = 2;

        // Enter the scale: min, max
        var yScale = d3.scale.linear()
                              .domain([0, d3.max(dataset, function(d){ return d;})])
                              .range([fontsize, h-titleFontSize]) // 20 so that a minimum of 20 px is reserved for the textvalue
                              .clamp(true);

        var colorScale = d3.scale.log()
                                .domain([0, d3.max(dataset, function(d){
                                    return d;
                                })])
                                .range([0,255])
                                .clamp(true);

        // Create and get the SVG element.
        var svg = d3.select("body")
                .append("svg")
                .attr("width", w)
                .attr("height", h);

        // Create the bars.
        svg.selectAll("rect")
            .data(dataset)
            .enter()
            .append("rect")
            .attr("x",function(d, i){
                return i * (w / dataset.length);
            })
            .attr("y", function(d){
                return  h - yScale(d);
            })
            .attr("width", w / dataset.length - barPadding)
            .attr("height",h)
            .attr("fill", function(d){
                return "rgb(0,0, " + (255 - Math.round(yScale(d))) + ")";
            })
            .append("svg:title")
            .text(function(d){
                return d;
            });

            // Show the value indicators
            svg.selectAll("text")
                .data(dataset)
                .enter()
                .append("text")
                .text(function(d){
                    return d;
                })
                .attr("x", function(d, i) {
                    return i * (w / dataset.length) + (w / dataset.length - barPadding) / 2;
                })
                .attr("y", function(d) {
                    return h - yScale(d) + 15;
                })
                .attr("font-family","sans-serif")
                .attr("font-size",fontsize + "px")
                .attr("fill", "white")
                .attr("font-weight","bold")
                .attr("class","shadow")
                .attr("text-anchor","middle");

            // Show the title
            svg.append("text")
                .attr("class", "title")
                .attr("x", w/2)
                .attr("y", 20)
                .attr("font-family","sans-serif")
                .attr("font-size", titleFontSize + "px")
                .attr("font-weight","bold")
                .attr("text-anchor","middle")
                .text("<?php echo $_GET['bar_value']; ?>");

    </script>

  </body>
</html>
<?php
        exit();
    }

    public function printHeader(){
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: text/html; charset=UTF-8");
    }

    /**
     * Get the data provided by the request parameter bar_value.
     * Return an array with the data to be displayed.
     */
    private function getData(){

        $property = $_GET["bar_value"];
        $rootname = $this->rootname;
        $data = $this->objectToPrint->$rootname;
        $datastring = ""; // Necessary to give back to our javascript.

        if(!empty($data) && is_array($data[0]) && !empty($data[0][$property])){
            foreach($data as $entry){
                if(!empty($entry[$property]) && $entry[$property] != 'null'){
                    $datastring.= $entry[$property] . ",";
                }
            }
        }else if(!empty($data) && is_object($data[0]) && !empty($data[0]->$property)){
            foreach($data as $entry){
                if(!empty($entry->$property) && $entry->$property != 'null'){
                    $datastring.= $entry->$property . ",";
                }
            }
        }

        $datastring = rtrim($datastring,",");
        return $datastring;
    }

    /**
     * Check if the bar_value parameter has been set, if so, also check if the property exists.
     */
    private function checkProperty(){
        if(empty($_GET["bar_value"])){
            echo "Pass along the field name of the data you want to see displayed. This is done by using bar_value as a request parameter.";
            die();
        }

        $property = $_GET["bar_value"];
        $rootname = $this->rootname;
        $data = $this->objectToPrint->$rootname;

        if(empty($data) || (is_array($data[0]) && empty($data[0][$property])) || (is_object($data[0]) && empty($data[0]->$property))) {
            echo "No data has been found for the field: " . $property . ".";
            die();
        }
    }
}