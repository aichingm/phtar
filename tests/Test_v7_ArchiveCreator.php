<?php

use \Pest\Utils;
use \phtar\utils\FileHandle;

$t = new Pest\Pest(substr(basename(__FILE__), 0, -4));
$databox = new stdClass();
require_once __DIR__ . '/../src/Autoload.php';


$t->test('Test if the phtar\v7\ArchiveCreator', function() use($t, $databox) {
    $filename = Utils::TMP_FILE("Tar");
    file_put_contents($filename, "");
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
    echo $filename;
    #Utils::RM_TMP_FILES();
});


$t->run();
