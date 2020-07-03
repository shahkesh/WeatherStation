<?php
    include_once('esp-database.php');
    
    if ($_GET["readingsCount"]){
      $data = $_GET["readingsCount"];
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      $readings_count = $_GET["readingsCount"];
    }
    
    else {
      $readings_count = 60;
    }

    $last_reading = getLastReadings();
    $last_reading_temp = $last_reading["value1"];
    $last_reading_humi = $last_reading["value2"];
    
    $last_readingLight = getLastReadingsLIGHT();
	$last_reading_light = $last_readingLight["value3"];
    //$last_reading_time = $last_reading["reading_time"];

    $min_temp = minReading($readings_count, 'value1');
    $max_temp = maxReading($readings_count, 'value1');
    $avg_temp = avgReading($readings_count, 'value1');

    $min_humi = minReading($readings_count, 'value2');
    $max_humi = maxReading($readings_count, 'value2');
    $avg_humi = avgReading($readings_count, 'value2');
    
    $min_light = minReading($readings_count, 'value3');
    $max_light = maxReading($readings_count, 'value3');
    $avg_light = avgReading($readings_count, 'value3');
?>

<!DOCTYPE html>
<html>
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

        <link rel="stylesheet" type="text/css" href="esp-style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    </head>
    <header class="header">
        <h1>ESP Weather Station</h1>
    </header>
<body>
    <section class="content">
	    <div class="box gauge--1">
	    <h3>TEMPERATURE</h3>
              <div class="mask">
			  <div class="semi-circle"></div>
			  <div class="semi-circle--mask"></div>
			</div>
		    <p style="font-size: 30px;" id="temp">--</p>
		    <table cellspacing="5" cellpadding="5">
		        <tr>
		            <td>Min</td>
                    <td>Max</td>
                    <td>Average</td>
                </tr>
                <tr>
                    <td><?php echo $min_temp['min_amount']; ?> &deg;C</td>
                    <td><?php echo $max_temp['max_amount']; ?> &deg;C</td>
                    <td><?php echo round($avg_temp['avg_amount'], 2); ?> &deg;C</td>
                </tr>
            </table>
        </div>
        <div class="box gauge--2">
            <h3>HUMIDITY</h3>
			<div class="mask">
                <div class="semi-circle"></div>
                <div class="semi-circle--mask"></div>
            </div>
            <p style="font-size: 30px;" id="humi">--</p>
            <table cellspacing="5" cellpadding="5">
                <tr>
                    <td>Min</td>
                    <td>Max</td>
                    <td>Average</td>
                </tr>
                <tr>
                    <td><?php echo $min_humi['min_amount']; ?> %</td>
                    <td><?php echo $max_humi['max_amount']; ?> %</td>
                    <td><?php echo round($avg_humi['avg_amount'], 2); ?> %</td>
                </tr>
            </table>
        </div>
        
        <div class="box gauge--3">
            <h3>LIGHT</h3>
			<div class="mask">
                <div class="semi-circle"></div>
                <div class="semi-circle--mask"></div>
            </div>
            <p style="font-size: 30px;" id="lightgauge">--</p>
            <table cellspacing="5" cellpadding="5">
                <tr>
                    <td>Min</td>
                    <td>Max</td>
                    <td>Average</td>
                </tr>
                <tr>
                    <td><?php echo $min_light['min_amount']; ?> LUX</td>
                    <td><?php echo $max_light['max_amount']; ?> LUX</td>
                    <td><?php echo round($avg_light['avg_amount'], 2); ?> LUX</td>
                </tr>
            </table>
        </div>
		
    </section>
<?php
    echo   '<h2> View Latest Readings</h2>
            <table cellspacing="5" cellpadding="5" id="tableReadings">
                <tr>
                    <th>ID</th>
                    <th>Sensor</th>
                    <th>Location</th>
                    <th>Temperatur</th>
                    <th>Luftfeuchtigkeit</th>
                    <th>Helligkeit</th>
                    <th>Timestamp</th>
                </tr>';

    $result = getAllReadings($readings_count);
    $valueFor3;
    $valueFor2;
    $valueFor1;
    $valuerow_id;
    $valuerow_sensor;
	$valuerow_location;
	
        if ($result) {
        while ($row = $result->fetch_assoc()) {
			
			if ($row["value3"] != null && $valueFor3 == null) {
				$valueFor3 = $row["value3"];
				//continue;
				
			}
				
			if($row["value1"] != null && $valueFor2 == null){
				$valueFor2 = $row["value2"];
				$valueFor1 = $row["value1"];
				$valuerow_id = $row["id"];
				$valuerow_sensor = $row["sensor"];
				$valuerow_location = $row["location"];
				continue;
			}
			
			if($valueFor1 != null){
					$row_value1 = $valueFor1;
					$row_value2 = $valueFor2;
					$row_value3 = $valueFor3;
					$valueFor3 = null;
                    $valueFor2 = null;
					//$row_id = $valuerow_id;
					$row_sensor= $valuerow_sensor;
					$row_location = $valuerow_location;
					$row_reading_time = $row["reading_time"];
					
					 echo
					'<tr>
                    <td>no id</td>
                    <td>' . $row_sensor . '</td>
                    <td>' . $row_location . '</td>
                    <td>' . $row_value1 . '</td>
                    <td>' . $row_value2 . '</td>
                    <td>' . $row_value3 . '</td>
                    <td>' . $row_reading_time . '</td>
					</tr>';
					
                  
                    
					
			}
			
        }
        echo '</table>'; 
        $result->free();
        
    }
?>

<script>
    var value1 = <?php echo $last_reading_temp; ?>;
    var value2 = <?php echo $last_reading_humi; ?>;
    //var value3 = <?php echo $last_reading_light; ?>;
	
    setTemperature(value1);
    setHumidity(value2);
    //setLight(value3);	
    
	

    function setTemperature(curVal){
    	var minTemp = -10.0;
    	var maxTemp = 90.0;
 
    	var newVal = scaleValue(curVal, [minTemp, maxTemp], [0, 180]);
    	$('.gauge--1 .semi-circle--mask').attr({
    		style: '-webkit-transform: rotate(' + newVal + 'deg);' +
    		'-moz-transform: rotate(' + newVal + 'deg);' +
    		'transform: rotate(' + newVal + 'deg);'
    	});
    	$("#temp").text(curVal + ' ºC');
    }

    function setHumidity(curVal){
    	var minHumi = 0;
    	var maxHumi = 100;

    	var newVal = scaleValue(curVal, [minHumi, maxHumi], [0, 180]);
    	$('.gauge--2 .semi-circle--mask').attr({
    		style: '-webkit-transform: rotate(' + newVal + 'deg);' +
    		'-moz-transform: rotate(' + newVal + 'deg);' +
    		'transform: rotate(' + newVal + 'deg);'
    	});
    	$("#humi").text(curVal + ' %');
    }
	
	/*function setLight(curVal){
    	var minlight = 1;
    	var maxlight = 1000;

    	var newVal = scaleValue(curVal, [minlight, maxlight], [0, 180]);
    	$('.gauge--3 .semi-circle--mask').attr({
    		style: '-webkit-transform: rotate(' + newVal + 'deg);' +
    		'-moz-transform: rotate(' + newVal + 'deg);' +
    		'transform: rotate(' + newVal + 'deg);'
    	});
    	$("#lightgauge").text(curVal + ' LUX');
    }*/

    function scaleValue(value, from, to) {
        var scale = (to[1] - to[0]) / (from[1] - from[0]);
        var capped = Math.min(from[1], Math.max(from[0], value)) - from[0];
        return ~~(capped * scale + to[0]);
    }
</script>
</body>
</html>
