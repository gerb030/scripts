#!/usr/local/bin/php
<?php
echo PHP_EOL;
$file = $argv[1];
$conn = mysql_connect ('localhost', 'gerb', 'geheim');
mysql_select_db('configar_usage', $conn);
$linesRead = 0;
$handle = @fopen($file, "r");
if ($handle) {
    while (($buffer = fgets($handle, 4096)) !== false) {
		processLine($buffer, $conn);
        $linesRead++;
        if ($linesRead % 100 == 0) print '.';
        if ($linesRead % 10000 == 0) echo PHP_EOL;
    }
    if (!feof($handle)) {
        echo "Error: unexpected fgets() fail\n";
    }
    fclose($handle);
}

function processLine($line, $conn) {
	$values = array();
	$line = substr($line, strpos($line, ": ")+2);
	$parts = explode(';', $line);
	if (count($parts) > 3) {
		foreach($parts as $part) {
			if (strlen($part) > 2) {
				$v = explode('=>', $part);
				if (count($v) == 2) {
					$values[$v[0]] = mysql_real_escape_string(trim($v[1]));
				}
			}
		}
		if (!isset($values['gameName'])) $values['gameName'] = 'NO_GAME';
		if (!isset($values['configarId'])) $values['configarId'] = 'NO_CONFIGAR_ID';
		if (!isset($values['referrerURL'])) $values['referrerURL'] = 'NO_REFERRER';
		if (!isset($values['BrandSystemUrl'])) $values['BrandSystemUrl'] = 'NO_BRANDSYSTEM';
		if (!isset($values['servicePackURL'])) $values['servicePackURL'] = 'NO_SERVICEPACK';
		if (!isset($values['apiLocation'])) $values['apiLocation'] = 'NO_APILOCATION';
		$game = readQuery(sprintf("SELECT id, name, configar_id, contentar_id FROM game WHERE name = '%s' AND configar_id = '%s'", $values['gameName'], $values['configarId']), $conn);
		if (count($game) == 0) {
			$gameId = writeQuery(sprintf("INSERT INTO game (name, configarId) VALUES ('%s', '%s');", $values['gameName'], $values['configarId']), $conn);
		} else {
			$gameId = $game[0]['id'];
		}
		$apiVersion = readQuery(sprintf("SELECT id, game_id, servicepack, api, brand 
										FROM game_apiversion 
										WHERE game_id = '%s' AND servicepack = '%s' AND api = '%s' AND brand = '%s' ", 
										$gameId, $values['servicePackURL'], $values['apiLocation'], $values['BrandSystemUrl']), $conn);
		if (count($apiVersion) == 0) {
			$gameApiversionId = writeQuery(sprintf("INSERT INTO game_apiversion (game_id, servicepack, api, brand)
										  VALUES ('%s', '%s', '%s', '%s');", $gameId, $values['servicePackURL'], $values['apiLocation'], $values['BrandSystemUrl']), $conn);
		} else {
			$gameApiversionId = $apiVersion[0]['id'];
		}
		$apiRequest = readQuery(sprintf("SELECT id, game_apiversion_id, url, instances
										FROM apiversion_request 
										WHERE game_apiversion_id = '%s' AND url = '%s'",
										$gameApiversionId, $values['referrerURL']), $conn);
		if (count($apiRequest) == 0) {
			writeQuery(sprintf("INSERT INTO apiversion_request (game_apiversion_id, url, instances)
								  VALUES ('%s', '%s', 1);", $gameApiversionId, $values['referrerURL']), $conn);
		} else {
			writeQuery(sprintf("UPDATE apiversion_request SET instances = instances+1 WHERE id = '%s'", $apiRequest[0]['id']), $conn);
		}
	}
}

function writeQuery($sql, $conn) {
	$result = mysql_query($sql, $conn);
	if (mysql_error($conn) != 0) {
		die("Error in query : ".mysql_error($conn)." - ".$sql);
	}
	return mysql_insert_id($conn);	
}

function readQuery($sql, $conn) {
	$data = array();
	$result = mysql_query($sql, $conn);
	if (!$result) {
		print("Error in query : ".mysql_error($conn)." - ".$sql.PHP_EOL);
		return array();
	}
	while ($row = mysql_fetch_assoc($result)) {
		array_push($data, $row);
	}
	mysql_free_result($result);
	return $data;
}

echo PHP_EOL;