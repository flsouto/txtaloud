<?php

$lines = file($argv[1]);
$output = "";
foreach($lines as $line){

	if(strstr($line,':')){
		$parts = explode(":",$line,2);
		$line = trim($parts[1]);
		$line = str_replace("“","",$line);
		$line = str_replace("„","",$line);
	}

	$output .= $line."\n";

}

echo $output;