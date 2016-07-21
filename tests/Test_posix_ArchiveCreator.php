<?php

/**
 * This file is part of Phtar
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
use \Pest\Utils;
use \phtar\utils\FileHandle;

$t = new Pest\Pest(substr(basename(__FILE__), 0, -4));
$databox = new stdClass();
require_once __DIR__ . '/../src/Autoload.php';

$t->test('Test the phtar\posix\ArchiveCreator::__construct() method', function() use($t, $databox) {

    $t->expectException(function() {
        $filename = Utils::TMP_FILE("Tar");
        $ac = new phtar\posix\ArchiveCreator(new FileHandle($fHandle = fopen($filename, "r")));
    }, \phtar\utils\PhtarException::class);

    $t->expectException(function() {
        $filename = Utils::TMP_FILE("Tar");
        $ac = new phtar\posix\ArchiveCreator(new FileHandle($fHandle = fopen($filename, "a+")));
    }, \phtar\utils\PhtarException::class);

    $t->noException(function() {
        $filename = Utils::TMP_FILE("Tar");
        $ac = new phtar\posix\ArchiveCreator(new FileHandle($fHandle = fopen($filename, "r+")));
    });
});

$t->test('Test the phtar\posix\ArchiveCreator', function() use($t, $databox) {
    $filename = Utils::TMP_FILE("Tar");
    #file_put_contents($filename, "");
    $ac = new phtar\posix\ArchiveCreator(new FileHandle($fHandle = fopen($filename, "r+")));
    $entry = new \phtar\posix\BaseEntry("test.file.txt", str_repeat("X", 512));
    $ac->add($entry);
    $ac->write();

    $fsize = filesize($filename);
    $t->assertSame($fsize % 512, 0);
    $t->assertSame($fsize, 2048);

    $file = file_get_contents($filename);
    $t->assertSame(strlen($file), 2048);


    $t->assertSame(substr($file, 512, 512), str_repeat("X", 512));
    $t->assertSame(substr($file, 1024, 1024), str_repeat(chr(0), 1024));

    $ae = new phtar\posix\ArchiveEntry(new \phtar\utils\StringCursor(substr($file, 0, 512)), new \phtar\utils\StringCursor(substr($file, 512, 512)));

    $t->assertSame($ae->getGroupId(), 0);
    $t->assertSame($ae->getUserId(), 0);
    $t->assertSame($ae->getLinkname(), "");
    $t->assertSame($ae->getMTime(), $entry->getMTime());
    $t->assertSame($ae->getMode(), 0600);
    $t->assertSame($ae->getSize(), 512);
    $t->assertSame($ae->getType(), \phtar\posix\Archive::ENTRY_TYPE_FILE);

    fclose($fHandle);
    Utils::RM_TMP_FILES();
});


$t->test('Test the phtar\posix\ArchiveCreator', function() use($t, $databox) {
    $filename = Utils::TMP_FILE("Tar");
    #file_put_contents($filename, "");
    $ac = new phtar\posix\ArchiveCreator(new FileHandle($fHandle = fopen($filename, "r+")));
    $ac->addWithParentDirectories($entry1 = new \phtar\posix\DirectoryEntry("Austria/Styria/Graz"));
    $ac->add($entry2 = new \phtar\posix\DirectoryEntry("Itali"));
    $ac->addWithParentDirectories($entry3 = new \phtar\posix\BaseEntry("Canada/Ontario.Toronto.file.txt", str_repeat("M", 3072)));

    $ac->addWithParentDirectories($entry6 = new \phtar\posix\BaseEntry("Ontario.Toronto.file.txt", str_repeat("M1B - M9W", 300)));
    $ac->add($entry4 = new \phtar\posix\BaseEntry("Itali/Rome.file.txt", str_repeat("Cesar ", 2048)));
    $ac->add($entry5 = new \phtar\posix\BaseEntry("Itali/Venice.file.txt", str_repeat("Enrico Dandolo ", 2048)));

    $ac->write();

    $t->assertSame(filesize($filename), 512 * 4 + 512 + 512 + 3072 + 512 + 3072 + 512 + 6 * 2048 + 512 + 15 * 2048 + 512 * 2);
    exec("file $filename", $output);
    $t->assertSame($output[0], "$filename: POSIX tar archive");

    fclose($fHandle);
    Utils::RM_TMP_FILES();
});

$t->test('Test too long file names', function() use($t, $databox) {
    $filename = Utils::TMP_FILE("Tar");
    $t->expectException(function()use($filename) {
        file_put_contents($filename, "");
        $ac = new phtar\posix\ArchiveCreator(new FileHandle($fHandle = fopen($filename, "r+")));
        $ac->add($entry1 = new \phtar\posix\BaseEntry("this_file_name_is_too_long_to_be_stored_in_a_po_tar_file_file_name_field_but_just_for_testing.file.txt", "some content"));
        $ac->write();
        fclose($fHandle);
    }, phtar\utils\PhtarException::class);

    $t->noException(function()use($filename) {
        file_put_contents($filename, "");
        $ac = new phtar\posix\ArchiveCreator(new FileHandle($fHandle = fopen($filename, "r+")));
        $ac->add($entry1 = new \phtar\posix\BaseEntry("this_file_name_is_too_long_to_be_stored_in_a_po_tar_file_file_name_field_but_just_for_testing.file.", "some content"));
        $ac->write();
        fclose($fHandle);
    });



    Utils::RM_TMP_FILES();
});









$t->run();
