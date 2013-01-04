#!/usr/bin/php
<?php
// put in your favourite json data file and get the peak concurrent players daily
date_default_timezone_set("Europe/Amsterdam");
$allData = array();
$json = @json_decode(@file_get_contents($argv[1]), true);
foreach($json[0]['datapoints'] as $range) {
    $numUsers = $range[0];
    $date = date('j F Y', $range[1]);
    if (!isset($allData[$date]))
        $allData[$date] = array('users' => round($range[0]), 'date' => date('H:i \o\n j F Y', $range[1]));
    else if ($numUsers > $allData[$date]['users']) $allData[$date] = array('users' => round($range[0]), 'date' => date('H:i \o\n j F Y', $range[1]));
}
foreach($allData as $block) echo 'At '.$block['date'].', concurrent users peaked at '.$block['users'].".\n";
exit(0);
