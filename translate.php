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

    foreach($lines2 as $i => $line){
        $line = trim($line);
        if(empty($line)){
            unset($lines2[$i]);
        }
    }

    if(!empty($argv[3])){
        $lines2 = array_slice($lines2,0,$argv[3]);
        $lines = array_slice($lines,0,$argv[3]);

        echo end($lines2).PHP_EOL;
        echo end($lines).PHP_EOL;

        if(empty($argv[4])){
            die();
        }
    }

    if(count($lines2)!=count($lines)){
        die("Mismatch on number of lines!\n");
    }

    $lines2 = array_values($lines2);

    foreach($data as $i => &$row){
        if(isset($lines2[$i])){
            $row['text'][$lang] = $lines2[$i];
        }
    }; unset($row);

    file_put_contents($output_file, json_encode($data));

}

