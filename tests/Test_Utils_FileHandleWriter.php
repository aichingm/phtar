<?php

/**
 * This file is part of Phtar
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
$t = new Pest\Pest(substr(basename(__FILE__), 0, -4));
$databox = new stdClass();
require_once __DIR__ . '/../src/Autoload.php';

use phtar\utils\FileHandleWriter;

$t->test("Test FileHandleWriter", function() use($t, $databox) {
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
    $filename = tempnam(sys_get_temp_dir(), 'FileHandleWriter');
    $handle = fopen($filename, "r+");
    fseek($handle, 0);
    //new FileHandleWriter
    $writer = new FileHandleWriter($handle);
    $t->assertSame($writer->getMode(), 'r+');
    $t->assertEquals($writer->write($testString), strlen($testString));
    $t->assertTrue($writer->flush());
    $t->assertEquals(file_get_contents($filename), $testString);
    $t->assertTrue($writer->seek(0) === 0);
    $t->assertEquals($writer->write("test"), 4);
    $t->assertTrue($writer->seek(0) === 0);
    $t->assertEquals(fread($handle, 5), "testm");

    $newWriter = clone $writer;
    $t->assertSame($newWriter->getMode(), 'r+');
    $t->assertEquals($newWriter->seek(0), 0);
    $t->assertEquals($writer->seek(1), 0);
    $t->assertEquals($newWriter->write("A"), 1);
    $t->assertEquals($writer->write("B"), 1);
    fseek($handle, 0);
    $t->assertEquals(fread($handle, 2), 'AB');

    fclose($handle);
    unlink($filename);
});

$t->run();

























