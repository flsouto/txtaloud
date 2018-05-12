<?php

if(empty($argv[1])){
    die("usage: command DIR\n");
}

$dir = $argv[1];
if(!is_dir($dir)){
    die("Invalid dir: $dir\n");
}

$sound_file = '';
$text_file = '';

foreach(scandir($dir) as $file){
    if($file=='.'||$file=='..'){
        continue;
    }

    if(strstr($file,'.mp3')||stristr($file,'.wav')){
        $sound_file = $dir.'/'.$file;
    }

    if(strstr($file,'.txt')){
        $text_file = $dir.'/'.$file;
    }

}

if(!$sound_file){
    die("Sound file missing\n");
}

if(!$text_file){
    die("Text file missing\n");
}

$text_lines = [];

foreach(file($text_file) as $line){
    $line = trim($line);
    if(empty($line)){
        continue;
    }
    $text_lines[] = $line;
}

$output = [];

if(file_exists($file = $dir.'/output.json')){
    $output = json_decode(file_get_contents($file), true);
}

if(file_exists($file = $dir.'/offsets.json')){
    $offsets = json_decode(file_get_contents($file), true);
} else {
    $offsets = ['text' => 0, 'sound' => 0];
}

$jump = 1;
$cut = 0;

$tmp_file = __DIR__.'/tmp.mp3';
$stdin = fopen("php://stdin", "r");

while(!empty($text_lines[$offsets['text']])){

    $offset = $offsets['sound'] + $cut;

    shell_exec("sox $sound_file $tmp_file trim $offset $jump");

    $text = $text_lines[$offsets['text']];

    echo $text."\n";

    shell_exec("play $tmp_file");

    $input = trim(fgets($stdin));
    $args = explode(" ", $input);
    $cmd = array_shift($args);

    if($cmd == 'c'){
        $cut = $args[0];
    } else if($cmd == 'j') {
        $jump += $args[0];
    } else if($cmd == 'b') {
        $jump -= $args[0];
    } else if($cmd == 's'){
        $sound = base64_encode(file_get_contents($tmp_file));
        $output[] = [
            'sound' => $sound,
            'text' => ['de'=>$text]
        ];
        $offsets['sound'] += $cut+$jump;
        $offsets['text']++;

        file_put_contents($dir.'/offsets.json', json_encode($offsets));
        file_put_contents($dir.'/output.json', json_encode($output));
        $cut = 0;
    }



}

