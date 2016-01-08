<?php

$t = new Pest\Pest(substr(basename(__FILE__), 0, -4));
$databox = new stdClass();
require_once __DIR__ . '/../src/Autoload.php';

use phtar\utils\FileHandleReader;

$t->test("Test FileHandleReader", function() use($t, $databox) {
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


    //create File
    $filename = tempnam(sys_get_temp_dir(), 'FileHandleReader');
    $handle = fopen($filename, "r+");
    fwrite($handle, $testString);
    fseek($handle, 0);
    //new FileHandleReader
    $reader = new FileHandleReader($handle);
    $t->assertFalse($reader->eof(FileHandleReader::EOF_MODE_EOF));
    $t->assertEquals($reader->length(), strlen($testString));
    $t->assertEquals($reader->getc(), 'L');
    $t->assertEquals($reader->gets(), "orem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod \n");
    $t->assertEquals($reader->seek(0), 0);
    $t->assertEquals($reader->getc(), 'L');
    $t->assertEquals($reader->seek(0), 0);
    $t->assertEquals($reader->read(0), "");
    $t->assertEquals($reader->read(strlen($testString)), $testString);
    $t->assertFalse($reader->eof(FileHandleReader::EOF_MODE_EOF));
    $t->assertTrue($reader->eof(FileHandleReader::EOF_MODE_LENGTH));
    $t->assertTrue($reader->eof(FileHandleReader::EOF_MODE_TRY_READ));
    $t->assertTrue($reader->eof(FileHandleReader::EOF_MODE_EOF));

    
    
    $newReader = clone $reader;
    $t->assertEquals($reader->seek(0), 0);
    $t->assertEquals($newReader->seek(0), 0);
    $t->assertEquals($reader->getc(), $newReader->getc());
    $t->assertEquals($newReader->seek(0), 0);
    $t->assertNotEquals($reader->getc(), $newReader->getc());



    fclose($handle);
    unlink($filename);
});

$t->run();

























