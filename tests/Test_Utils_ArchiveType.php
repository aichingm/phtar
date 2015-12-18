<?php

$t = new Pest\Pest(substr(basename(__FILE__), 0, -4));
$databox = new stdClass();
require_once __DIR__ . '/../src/Autoload.php';


$t->test("Test v7 archives", function() use($t, $databox) {

    $filename = tempnam(sys_get_temp_dir(), 'Tar');
    exec("bsdtar --format=v7 -cf $filename " . __FILE__);
    $fHandle = fopen($filename, "r");
    $handle = new \phtar\utils\FileHandle($fHandle);
    $t->assertEquals(\phtar\utils\ArchiveType::entryType($handle), \phtar\utils\ArchiveType::TYPE_V7);
    fclose($fHandle);
    unlink($filename);
});

$t->test("Test v7_gtar archives", function() use($t, $databox) {

    $filename = tempnam(sys_get_temp_dir(), 'Tar');
    exec("tar --format=v7 -cf $filename " . __FILE__);
    $fHandle = fopen($filename, "r");
    $handle = new \phtar\utils\FileHandle($fHandle);
    $t->assertEquals(\phtar\utils\ArchiveType::entryType($handle), \phtar\utils\ArchiveType::TYPE_V7_GTAR);
    fclose($fHandle);
    unlink($filename);
});

$t->test("Test posix archives", function() use($t, $databox) {

    $filename = tempnam(sys_get_temp_dir(), 'Tar');
    exec("bsdtar --format=ustar -cf $filename " . __FILE__);
    $fHandle = fopen($filename, "r");
    $handle = new \phtar\utils\FileHandle($fHandle);
    $t->assertEquals(\phtar\utils\ArchiveType::entryType($handle), \phtar\utils\ArchiveType::TYPE_POSIX_USTAR);
    fclose($fHandle);
    unlink($filename);
});

$t->test("Test gnu archives", function() use($t, $databox) {

    $filename = tempnam(sys_get_temp_dir(), 'Tar');
    exec("tar --format=gnu -cf $filename " . __FILE__);
    $fHandle = fopen($filename, "r");
    $handle = new \phtar\utils\FileHandle($fHandle);
    $t->assertEquals(\phtar\utils\ArchiveType::entryType($handle), \phtar\utils\ArchiveType::TYPE_GNU);
    fclose($fHandle);
    unlink($filename);
});

$t->run();





























