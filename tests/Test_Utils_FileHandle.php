<?php

/**
 * This file is part of Phtar
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
$t = new Pest\Pest(substr(basename(__FILE__), 0, -4));
$databox = new stdClass();
require_once __DIR__ . '/../src/Autoload.php';

use phtar\utils\FileHandle;

$t->test("Test FileHandle", function() use($t, $databox) {
    //create tmp file
    $filename = tempnam(sys_get_temp_dir(), 'File');
    //read file size
    $filesize = filesize($filename);
    //open tmp file
    $fHandle = fopen($filename, "r+");
    //new FileHandle
    $handle = new FileHandle($fHandle);
    //test FileHandle::write
    $t->assertEquals($handle->write("This is a test"), 14);
    //test FileHandle::flash
    $t->assertTrue($handle->flush());
    //test the files contents
    $t->assertEquals(file_get_contents($filename), "This is a test");
    //clear php's stat cache to make sure that the file size is not cached,
    clearstatcache();
    //test the new file size
    $t->assertEquals(filesize($filename), $filesize + 14);
    //test the mode
    $t->assertEquals($handle->getMode(), 'r+');
    //close the resource
    fclose($fHandle);
    //remove the tmp file
    unlink($filename);
});

$t->run();

























