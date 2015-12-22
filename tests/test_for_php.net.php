<?php

$testString = <<<EOF
Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod 
tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At 
vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd 
gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem 
ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy 
eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam 
voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita 
kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem 
ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod 
tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At 
vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, 
no sea takimata sanctus est Lorem ipsum dolor sit amet. 
Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie 
consequat, vel illum dolore eu f
EOF;


//create file
$filename = tempnam(sys_get_temp_dir(), 'ReadFile');
$handle = fopen($filename, "r+");
//setup test file
fwrite($handle, $testString);
fseek($handle, 0);

/*
 * Run tests
 */

echo "pointer is at 0 so not eof "; var_dump(feof($handle) === false);
echo "check if fread reads the same as fwrite writes "; var_dump(fread($handle, strlen($testString)) == $testString);
echo "check if pointer(ftell) is the the end of the file(length=1011) "; var_dump(ftell($handle) == 1011);
echo "feof should be true "; var_dump(feof($handle) == true); # this is the bad line
echo "check if end of file with fgetc == false "; var_dump(fgetc($handle) == false);
echo "recheck feof "; var_dump(feof($handle) == true);

//remove file
fclose($handle);
unlink($filename);

























