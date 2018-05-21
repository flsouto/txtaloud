<?php

if(empty($argv[1])){
    die("usage: command DIR\n");
}

$dir = $argv[1];
if(!is_dir($dir)){
    die("Invalid dir: $dir\n");
}

$output_file = $dir."/output.json";
if(!file_exists($output_file)){
	die("No output file found\n");
}

$data = file_get_contents($output_file);
$data = json_decode($data,true);

$index = $argv[2] ?? array_rand($data);
if(!isset($data[$index])){
	die("Undefined index: $index\n");
}

echo 'Total: '.count($data).' | Playing: '.$index.PHP_EOL;

$row = $data[$index];

foreach($row['text'] as $k=>$v){
    echo $k.': '.$v.PHP_EOL;
}

echo PHP_EOL;

$audio = base64_decode($row['sound']);
file_put_contents("tmp.mp3", $audio);
shell_exec("play tmp.mp3");
















