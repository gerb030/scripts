#!/usr/bin/php
<?php
# simple script that recurses throught the given directory and lists possible duplicates by checking file size. 
$readdir = $argv[1];
$minimalSize = (isset($argv[2]) ? $argv[2] : 0);
if (!file_exists($readdir) || !is_dir($readdir)) die("No such directory exists".PHP_EOL);
$duplicates = array();
$files = get_files_and_size_from_dir($readdir, $minimalSize);
foreach($files as $key1 => $file1) {
	foreach($files as $key2 => $file2) {
		if ($key1 != $key2 && $file1['size'] == $file2['size']) {
			if (!array_key_exists($file1['size'], $duplicates)) {
				$duplicates[$file1['size']] = array();
			}
			if (!in_array($file1['file'], $duplicates[$file1['size']])) {
				array_push($duplicates[$file1['size']], $file1['file']);
			}
			if (!in_array($file2['file'], $duplicates[$file1['size']])) {
				array_push($duplicates[$file1['size']], $file2['file']);
			}
		}
	}
}
foreach($duplicates as $size => $duplicate) {
	echo 'Size '.$size.' has '.count($duplicate).' files:'.PHP_EOL."\t";
	echo join(PHP_EOL."\t", $duplicate);
	echo PHP_EOL;
}


function get_files_and_size_from_dir($dir, $minimalSize) {
	$files = array();
	if ($handle = opendir($dir)) {
	    while (false !== ($entry = readdir($handle))) {
	        if (substr($entry, 0, 1) != '.') {
	        	if (is_dir($dir.'/'.$entry)) {
					$recursed = get_files_and_size_from_dir($dir.'/'.$entry, $minimalSize);
	        		foreach($recursed as $key => $value) {
	        			array_push($files, $value);
	        		}
	        	} else {
	        		$size = filesize($dir.'/'.$entry);
	        		if ($size > $minimalSize) {
	            		array_push($files, array('file' => substr($dir.'/'.$entry, 3), 'size' => $size));
	            	}
	            }
	        }
	    }		
	    closedir($handle);
   	} else {
		die(sprintf("Directory %s is not readable", $dir));
	}
	return $files;
}
echo PHP_EOL;