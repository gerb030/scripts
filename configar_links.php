#!/usr/local/bin/php
<?php
/**
* Finding contentar IDs that are linked to Configar IDs
*/
echo PHP_EOL;
$conn = mysql_connect ('localhost', 'gerb', 'geheim');
mysql_select_db('configar_analysis', $conn);

$sql = "select g_a.game_id, a_r.id as apiversion_request_id, a_r.url from apiversion_request a_r inner join game_apiversion g_a on (g_a.id = a_r.game_apiversion_id) where g_a.game_id = '%s' AND a_r.id > 99824";
$res = readQuery("select id as game_id, name, configar_id from game where contentar_id IS NULL", $conn);
foreach($res as $index => $row) {
	$urlRes = readQuery(sprintf($sql, $row['game_id']), $conn);
	if (count($urlRes) > 0) {
		$contentarIds = array();
		echo 'now trying '.count($urlRes).' URLs for '.$row['name'].PHP_EOL;
		foreach($urlRes as $key => $urlRow) {
			echo '  '.$urlRow['url'].' -> ';
			$contentarId = getContentarId($urlRow['url']);
			if ($contentarId != null) {
				echo '>>>> '.$contentarId.' <<<<'.PHP_EOL;
				writeQuery(sprintf("INSERT IGNORE INTO game_contentar (game_id, contentar_id, apiversion_request_id)
									  VALUES ('%s', '%s', '%s');", $row['game_id'], trim($contentarId), $urlRow['apiversion_request_id']), $conn);
			} else {
				echo $contentarId.PHP_EOL;
			}
		}
		echo $row['game_id'].' ('.$row['name'].') is now '.join(', ', $contentarIds).PHP_EOL;
	}
}



function getContentarId($url) {
	if (strtolower(substr($url, -3)) == 'swf') {
		return null;
	}
	$ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0); /* include headers from response in output */
    curl_setopt($ch, CURLOPT_VERBOSE, 0); /* don't write out verbose stuff to output */
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); /* don't verify ssl stuff (are we sure we want this?) */
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); /* return response as string from exec, no direct output */
    curl_setopt($ch, CURLINFO_HEADER_OUT, 1); /* track headers send in request */

    /* load balancers should timeout in 5 seconds, so 6 seconds gives them time to respond with an error */
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); /* number of seconds to wait while trying to connect */
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); /* number of seconds to wait for any curl function to execute */

    $output = curl_exec($ch);
    $details = curl_getinfo($ch);	
    curl_close($ch);
	$pr = preg_match("/\<meta property=\"game\:id\" content=\"([0-9]+)\"/", $output, $matches);
	if (count($matches)>0 && intval($matches[1]) > 0) return $matches[1];
	return null;
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