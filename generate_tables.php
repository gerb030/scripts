<?php
$maxSums = 30;
$cols = 3;
$pages = 10;
$mTablesConfig = array(
	2 => array('total' => 1),
	3 => array('total' => 9),
	4 => array('total' => 5),
	5 => array('total' => 2),
	6 => array('total' => 8),
	7 => array('total' => 12),
	8 => array('total' => 12),
	9 => array('total' => 12)
);

// 12		$totals
// $x		100

$totals = 0;
foreach($mTablesConfig as $mTableNr => $mTableWeight) {
	$totals = $totals + $mTableWeight['total'];
} 
$cTotals = 0;
foreach($mTablesConfig as $mTableNr => $mTableWeight) {
	$mTablesConfig[$mTableNr]['relative'] = (round($mTableWeight['total']*100/$totals)/100);
	$cTotals = $cTotals + $mTablesConfig[$mTableNr]['relative'];
} 
require_once('/Users/gerb/development/php/fpdf17/fpdf.php');
$doc = new FPDF();
$doc->SetFont('Times','',16);
for($p=0;$p<$pages;$p++) {
	$allAvailable = array();
	foreach($mTablesConfig as $mTableNr => $mTableWeight) {
		$thisTable = array();
		for($t=1;$t<=10;$t++) {
			array_push($thisTable, $t.' x '.$mTableNr.' =');
		}
		$allAvailable[$mTableNr] = $thisTable;
	}
	$colledTables = array();
	for($c=0;$c<$cols;$c++) {
		$tables = array();	
		$allAvailableHere = $allAvailable;
		foreach($mTablesConfig as $mTableNr => $mTableWeight) {
			$sumsToAdd = round($mTableWeight['relative'] * $maxSums);
			for($s=0;$s<$sumsToAdd;$s++) {
				array_push($tables, array_shift($allAvailableHere[$mTableNr]));
			}
		}
		shuffle($tables);
		array_push($colledTables, $tables);
	}
	$verticalledTables = array();
	foreach($colledTables as $col => $sums) {
		foreach($sums as $index => $sum) {
			$verticalledTables[$index][$col] = $sum;
		}
	}	

	// generate PDF
	$doc->AddPage();
	foreach($verticalledTables as $index => $colled) {
		foreach($colled as $sum) {
	  		$doc->Cell(62,8,$sum,0);
		}
		$doc->Ln();
	}
}
$doc->Output('sommen.pdf', 'F');
// $body = '<table><tr>';
// foreach($colledTables as $col => $sums) {
// 	$body .= '<td>'.join($sums, '<br>').'</td>';
// }
// $body .= '</tr><table>';


// file_put_contents('sommen.pdf', $body);
