<?php

use \Pest\Utils;
use \phtar\utils\FileHandle;

$t = new Pest\Pest(substr(basename(__FILE__), 0, -4));
$databox = new stdClass();
require_once __DIR__ . '/../src/Autoload.php';


$t->test('Test the phtar\v7\ArchiveCreator', function() use($t, $databox) {
    $filename = Utils::TMP_FILE("Tar");
    #file_put_contents($filename, "");
    $ac = new phtar\v7\ArchiveCreator(new FileHandle($fHandle = fopen($filename, "r+")));
    $entry = new \phtar\v7\BaseEntry("test.file.txt", str_repeat("X", 512));
    $ac->add($entry);
    $ac->write();

    $fsize = filesize($filename);
    $t->assertEquals($fsize % 512, 0);
    $t->assertEquals($fsize, 2048);

    $file = file_get_contents($filename);
    $t->assertEquals(strlen($file), 2048);


    $t->assertEquals(substr($file, 512, 512), str_repeat("X", 512));
    $t->assertEquals(substr($file, 1024, 1024), str_repeat(chr(0), 1024));

    $ae = new phtar\v7\ArchiveEntry(new \phtar\utils\StringCursor(substr($file, 0, 512)), new \phtar\utils\StringCursor(substr($file, 512, 512)));

    $t->assertEquals($ae->getGroupId(), 0);
    $t->assertEquals($ae->getUserId(), 0);
    $t->assertEquals($ae->getLinkname(), "");
    $t->assertEquals($ae->getMTime(), $entry->getMTime());
    $t->assertEquals($ae->getMode(), 0755);
    $t->assertEquals($ae->getSize(), 512);
    $t->assertEquals($ae->getType(), \phtar\v7\Archive::ENTRY_TYPE_FILE);

    fclose($fHandle);
    Utils::RM_TMP_FILES();
});


$t->test('Test the phtar\v7\ArchiveCreator', function() use($t, $databox) {
    $filename = Utils::TMP_FILE("Tar");
    #file_put_contents($filename, "");
    $ac = new phtar\v7\ArchiveCreator(new FileHandle($fHandle = fopen($filename, "r+")));
    $ac->addWithParentDirectories($entry1 = new \phtar\v7\DirEntry("Austria/Styria/Graz"));
    $ac->add($entry2 = new \phtar\v7\DirEntry("Itali"));
    $ac->addWithParentDirectories($entry3 = new \phtar\v7\BaseEntry("Canada/Ontario.Toronto.file.txt", str_repeat("M", 3072)));

    $ac->addWithParentDirectories($entry6 = new \phtar\v7\BaseEntry("Ontario.Toronto.file.txt", str_repeat("M1B - M9W", 300)));
    $ac->add($entry4 = new \phtar\v7\BaseEntry("Itali/Rome.file.txt", str_repeat("Cesar ", 2048)));
    $ac->add($entry5 = new \phtar\v7\BaseEntry("Itali/Venice.file.txt", str_repeat("Enrico Dandolo ", 2048)));

    $ac->write();

    $t->assertEquals(filesize($filename), 512 * 4 + 512 + 512 + 3072 + 512 + 3072 + 512 + 6 * 2048 + 512 + 15 * 2048 + 512 * 2);


    fclose($fHandle);
    Utils::RM_TMP_FILES();
});

$t->test('Test too long file names', function() use($t, $databox) {
    $filename = Utils::TMP_FILE("Tar");
    $t->expectException(function()use($filename) {
        file_put_contents($filename, "");
        $ac = new phtar\v7\ArchiveCreator(new FileHandle($fHandle = fopen($filename, "r+")));
        $ac->add($entry1 = new \phtar\v7\BaseEntry("this_file_name_is_too_long_to_be_stored_in_a_v7_tar_file_file_name_field_but_just_for_testing.file.txt", "some content"));
        $ac->write();
        fclose($fHandle);
    }, phtar\utils\TarException::class);
    
    $t->noException(function()use($filename) {
        file_put_contents($filename, "");
        $ac = new phtar\v7\ArchiveCreator(new FileHandle($fHandle = fopen($filename, "r+")));
        $ac->add($entry1 = new \phtar\v7\BaseEntry("this_file_name_is_too_long_to_be_stored_in_a_v7_tar_file_file_name_field_but_just_for_testing.file.", "some content"));
        $ac->write();
        fclose($fHandle);
    });
    
    
    
    Utils::RM_TMP_FILES();
});









$t->run();
