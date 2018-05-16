<?php

if(empty($argv[1])){
    die("Usage: command FILE_TO_SEND\n");
}

echo shell_exec("curl -X PUT http://htput.com/txtaloud/data --data-binary \"@".$argv[1]."\" --header \"Htput-pass: 17ad925\"");