<?php

/**
 * This file is part of Phtar
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
$t = new Pest\Pest(substr(basename(__FILE__), 0, -4));
$databox = new stdClass();
require_once __DIR__ . '/../src/Autoload.php';

use phtar\utils\ArchiveType;

$t->test("Test v7 archives", function() use($t, $databox) {

    $filename = tempnam(sys_get_temp_dir(), 'Tar');
    exec("bsdtar --format=v7 -cf $filename " . basename(__FILE__));
    $fHandle = fopen($filename, "r");
    $handle = new \phtar\utils\FileHandle($fHandle);
    $t->assertEquals(ArchiveType::entryType($handle), ArchiveType::TYPE_V7);
    fclose($fHandle);
    unlink($filename);
});

$t->test("Test v7_gtar archives", function() use($t, $databox) {

    $filename = tempnam(sys_get_temp_dir(), 'Tar');
    exec("tar --format=v7 -cf $filename " . basename(__FILE__));
    $fHandle = fopen($filename, "r");
    $handle = new \phtar\utils\FileHandle($fHandle);
    $t->assertEquals(ArchiveType::entryType($handle), ArchiveType::TYPE_V7_GTAR);
    fclose($fHandle);
    unlink($filename);
});

$t->test("Test posix archives", function() use($t, $databox) {

    $filename = tempnam(sys_get_temp_dir(), 'Tar');
    exec("bsdtar --format=ustar -cf $filename " . basename(__FILE__));
    $fHandle = fopen($filename, "r");
    $handle = new \phtar\utils\FileHandle($fHandle);
    $t->assertEquals(ArchiveType::entryType($handle), ArchiveType::TYPE_POSIX_USTAR);
    fclose($fHandle);
    unlink($filename);
});

$t->test("Test gnu archives", function() use($t, $databox) {

    $filename = tempnam(sys_get_temp_dir(), 'Tar');
    exec("tar --format=gnu -cf $filename " . basename(__FILE__));
    $fHandle = fopen($filename, "r");
    $handle = new \phtar\utils\FileHandle($fHandle);
    $t->assertEquals(ArchiveType::entryType($handle), ArchiveType::TYPE_GNU);
    fclose($fHandle);
    unlink($filename);
});


$t->test("Test onlyContains", function() use($t, $databox) {

    $t->assertTrue(ArchiveType::onlyContains(str_repeat("\0", 512), "\0"));
    $t->assertTrue(ArchiveType::onlyContains(str_repeat("\0", 511), "\0"));
    $t->assertTrue(ArchiveType::onlyContains(str_repeat("\0", 513), "\0"));
    $t->assertTrue(ArchiveType::onlyContains(str_repeat(" ", 7), " "));
    $t->assertFalse(ArchiveType::onlyContains("This is a test", " "));
    $t->assertFalse(ArchiveType::onlyContains("      \0", " "));
});

$t->run();





























