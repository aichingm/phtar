<?php

/**
 * This file is part of Phtar
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
$t = new Pest\Pest(substr(basename(__FILE__), 0, -4));
$databox = new stdClass();
require_once __DIR__ . '/../src/Autoload.php';

use phtar\utils\FileHandleHelper;

$t->test("Test FileHandleHelper::COPY_H2H", function() use($t, $databox) {
    //source handle
    $sfHandle = fopen(__FILE__, "r");
    $sourceHandle = new phtar\utils\FileHandle($sfHandle);
    //destination handle
    $filename = tempnam(sys_get_temp_dir(), 'COPY_H2H');
    $dfHandle = fopen($filename, "r+");
    $destHandle = new phtar\utils\FileHandle($dfHandle);
    //file size
    $filesize = filesize(__FILE__);
    //test the copy function
    //test the writen length
    $t->assertEquals(FileHandleHelper::COPY_H2H($sourceHandle, $destHandle), $filesize);
    //test the contents
    $t->assertEquals(md5_file(__FILE__), md5_file($filename));
    //close & remove
    fclose($sfHandle);
    fclose($dfHandle);
    unlink($filename);
});

$t->test("Test FileHandleHelper::CLONE_HANDLE", function() use($t, $databox) {
    $filename = tempnam(sys_get_temp_dir(), 'CLONE_HANDLE');
    $handle = fopen($filename, "r+");
    $handleCopy = FileHandleHelper::CLONE_HANDLE($handle);

    $t->assertNotEquals((int) $handle, (int) $handleCopy);
    $t->assertEquals(fwrite($handle, "This is a test\n"), 15);
    $t->assertEquals(ftell($handle), 15);
    $t->assertEquals(ftell($handleCopy), 0);
    $t->assertEquals(fgets($handleCopy), "This is a test\n");

    //close the handles 
    fclose($handle);
    fclose($handleCopy);
});
$t->run();

























