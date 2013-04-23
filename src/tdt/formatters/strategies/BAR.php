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

        if(dataset.length == 0){
            d3.select("body")
                .append("p")
                .text("No plottable data was found.");
            return;
        }

        var w = 1000;
        var h = 200;
        var fontsize = 12;
        var titleFontSize = 16;
        var labelPadding = 30;
        var barPadding = 2;

        // Enter the scale: min, max
        var yScale = d3.scale.linear()
                              .domain([0, d3.max(dataset, function(d){ return d;})])
                              .range([25, h-25]) // Save space to display the value field (title)
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
        $datastring = ""; // Builds a string to return to the javascript.

         // Peek at the first item.
        if(is_object($data)){
            $first_item = $data;
        }else{
            $first_item = array_shift($data);
            array_unshift($data, $first_item);
        }

        // This is when the consumer asks a bar chart on 1 object or piece of data
        // For now we're returning that one piece of data, although it may not seem very significant to build
        // a bar chart out of one property.
        if(is_object($data)){
            if(!empty($data->$property)){
                return $data->$property;
            }
        }

        if(!empty($data) && is_array($first_item) && !empty($first_item[$property])){
            foreach($data as $key => $entry){
                if(!empty($entry[$property]) && $entry[$property] != 'null'){
                    $datastring.= $entry[$property] . ",";
                }
            }
        }else if(!empty($data) && is_object($first_item) && !empty($first_item->$property)){
            foreach($data as $key => $entry){
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

        // Peek at the first item.
        if(is_object($data) && empty($data->$property)){
            echo "No data has been found for the field: " . $property;
            die();
        }else if(!is_object($data)){
            $first_item = array_shift($data);

            if(empty($data) || (is_array($first_item) && empty($first_item[$property])) || (is_object($first_item) && empty($first_item->$property))) {
                echo "No data has been found for the field: " . $property . ".";
                die();
            }

            array_unshift($data,$first_item);
        }
    }

    /**
     * Return some information about this formatter.
     */
    public static function getDocumentation(){
        return "This formatter returns a bar chart based on the field passed with the 'bar_value' request parameter.";
    }
}