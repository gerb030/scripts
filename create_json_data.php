#!/usr/bin/php
<?php
if (!isset($argv[2])) {
	echo "USAGE : ".basename($argv[0])." [TARGET_JSON_FILE] [CSV|JSON]\n";
	echo str_repeat("-", 58).PHP_EOL;
	exit(0);
}
// put in your favourite json data file and get the peak concurrent players daily
date_default_timezone_set("Europe/Amsterdam");
$allData = array();
$json = @json_decode(@file_get_contents($argv[1]), true);
foreach($json[0]['datapoints'] as $range) {
    $numUsers = $range[0];
    $date = date('Y-m-d', $range[1]);
    if (!isset($allData[$date])) {
        $allData[$date] = round($range[0]);
    } else if ($numUsers > $allData[$date]) {
	$allData[$date] = round($range[0]);
    }
}
array_pop($allData);
switch(strtolower($argv[2])) {
	case 'csv':
		foreach($allData as $k => $v) {
			echo $k.','.$v.PHP_EOL;
		}
		break;
	case 'json':
	default:
		$final = array();
		foreach($allData as $k => $v) {
			array_push($final, array($k, $v));
		}
		echo json_encode($final);
#		echo '{'.join($final, ',').'};'.PHP_EOL;
		break;
}
exit(0);
