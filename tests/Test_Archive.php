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
    $t->assertTrue(phtar\Archive::OPEN_BY_FILE_NAME($filename) instanceof \phtar\v7\Archive);
    $t->assertTrue(phtar\Archive::OPEN_BY_STRING(file_get_contents($filename)) instanceof \phtar\v7\Archive);
    unlink($filename);
});

$t->test("Test v7_gtar archives", function() use($t, $databox) {
    $filename = tempnam(sys_get_temp_dir(), 'Tar');
    exec("tar --format=v7 -cf $filename " . basename(__FILE__));
    $t->assertTrue(phtar\Archive::OPEN_BY_FILE_NAME($filename) instanceof \phtar\v7\Archive);
    $t->assertTrue(phtar\Archive::OPEN_BY_STRING(file_get_contents($filename)) instanceof \phtar\v7\Archive);
    unlink($filename);
});

$t->test("Test posix archives", function() use($t, $databox) {
    $filename = tempnam(sys_get_temp_dir(), 'Tar');
    exec("bsdtar --format=ustar -cf $filename " . basename(__FILE__));
    $t->assertTrue(phtar\Archive::OPEN_BY_FILE_NAME($filename) instanceof \phtar\posix\Archive);
    $t->assertTrue(phtar\Archive::OPEN_BY_STRING(file_get_contents($filename)) instanceof \phtar\posix\Archive);
    unlink($filename);
});

$t->test("Test gnu archives", function() use($t, $databox) {
    $filename = tempnam(sys_get_temp_dir(), 'Tar');
    exec("tar --format=gnu -cf $filename " . basename(__FILE__));
    $t->assertTrue(phtar\Archive::OPEN_BY_FILE_NAME($filename) instanceof \phtar\gnu\Archive);
    $t->assertTrue(phtar\Archive::OPEN_BY_STRING(file_get_contents($filename)) instanceof \phtar\gnu\Archive);
    unlink($filename);
});

$t->run();





























