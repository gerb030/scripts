<?php
$maxSums = 30;
$cols = 2;
$mTablesConfig = array(
	2 => 1,
	3 => 5,
	5 => 8,
	6 => 10,
	7 => 12,
	8 => 12,
	9 => 9
);

$colledTables = array();
$totals = 0;
foreach($mTablesConfig as $mTableNr => $mTableWeight) {
	$totals = $totals + $mTableWeight;
} 
$cTotals = 0;
foreach($mTablesConfig as $mTableNr => $mTableWeight) {
	$mTablesConfig[$mTableNr] = ceil($mTableWeight*100/$totals);
	$cTotals = $cTotals + $mTablesConfig[$mTableNr];
} 
$allAvailable = array();
foreach($mTablesConfig as $mTableNr => $mTableWeight) {
	for($t=1;$t<=10;$t++) {
		array_push($allAvailable, $mTableNr.' x '.$t.' =');
	}
}

for($c=0;$c<$cols;$c++) {
	$tables = array();	
	for($s=0;$s<$maxSums;$s++) {
		
	}
}
