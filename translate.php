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

$lines = [];
foreach($data as $row){
    $lines []= current($row['text']);
}

if(empty($argv[2])){
    echo implode("|",$lines);
} else {

    $lang = $argv[2];
    if(strlen($lang)!=2){
        die("Language must have two chars, eg.: en, de, pt, etc...\n");
    }

    if(!file_exists($file=$dir.'/'.$lang.'.txt')){
        die("Translation file not found: $file\n");
    }

    $contents = file_get_contents($file);
    $lines2 = explode('|', $contents);

    if(count($lines2)!=count($lines)){
        die("Mismatch on number of lines!\n");
    }
    
    foreach($data as $i => &$row){
        $row['text'][$lang] = $lines2[$i];
    }; unset($row);

    file_put_contents($output_file, json_encode($data));

}

