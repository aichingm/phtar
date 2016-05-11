<?php

/**
 * This file is part of Phtar
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
use \Pest\Utils;
use \phtar\v7\Archive;
use \phtar\utils\FileHandle;

$t = new Pest\Pest(substr(basename(__FILE__), 0, -4));
$databox = new stdClass();
require_once __DIR__ . '/../src/Autoload.php';
define("ARCHIVE_FORMART", "--format=v7");
define("ENV_CRATION_FILE", __DIR__ . '/assets/setup.env.v7.php');
$t->test('Test if the phtar\v7\Archive contains all files', function() use($t, $databox) {
    $filename = Utils::RUN_IN(function() {
                require ENV_CRATION_FILE;
                exec("bsdtar " . ARCHIVE_FORMART . " -cf " . ($f = Utils::TMP_FILE('Tar')) . " " . ENV_NAME);
                return $f;
            });
    $archive = new Archive(new FileHandle($fHandle = fopen($filename, "r")));

    $t->assertSame(filesize($filename) % 512, 0);

    $t->assertSame(filesize($filename), 25600);


    $filelist = array(
        "env.v7/",
        "env.v7/SLink_long",
        "env.v7/HLink_long",
        "env.v7/this_is_a_long_dir/",
        "env.v7/mode755/",
        "env.v7/dir/",
        "env.v7/mode555/",
        "env.v7/mode777/",
        "env.v7/dir/in/",
        "env.v7/dir/in/dir/",
        "env.v7/dir/in/dir/CMtime.txt",
        "env.v7/mode755/CMtime.txt",
        "env.v7/mode755/SLink.B",
        "env.v7/mode755/HLink.B",
        "env.v7/mode755/B.txt",
        "env.v7/mode755/AB.txt",
        "env.v7/mode755/A.txt",
        "env.v7/this_is_a_long_dir/this_is_a_long_dir/",
        "env.v7/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/",
        "env.v7/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/",
        "env.v7/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/FILE.txt"
    );

    //test if all files are recognised 
    $size = 0;
    foreach ($archive as $file) {
        $size++;
    }
    $t->assertSame($size, count($filelist));
    //test if al files from the list are present
    foreach ($archive as $name => $file) {
        $pos = array_search($name, $filelist);
        unset($filelist[$pos]);
    }
    $t->assertEmpty($filelist);

    fclose($fHandle);
    Utils::RM_TMP_FILES();
    Utils::RM_RF(sys_get_temp_dir() . DIRECTORY_SEPARATOR . ENV_NAME);
});


$t->test('Test if the phtar\v7\Archive contains correct files and directories', function() use($t, $databox) {
    $filename = Utils::RUN_IN(function() {
                require ENV_CRATION_FILE;
                exec("bsdtar " . ARCHIVE_FORMART . " -cf " . ($f = Utils::TMP_FILE('Tar')) . " " . ENV_NAME);
                return $f;
            });
    $archive = new Archive(new FileHandle($fHandle = fopen($filename, "r")));


    $x = $archive->find("env.v7/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/FILE.txt");
    $t->assertSame($x->getName(), "env.v7/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/FILE.txt");
    $t->assertSame($x->getSize(), 0);
    $t->assertSame($x->getMTime(), strtotime("1992:06:23 14:12:00"));
    $t->assertSame($x->getMode(), 0755);
    $t->assertSame($x->getUserId(), posix_getuid());
    $t->assertSame($x->getGroupId(), posix_getgid());
    $t->assertFalse($x->getc());
    $t->assertSame($x->read(20), "");
    $t->assertSame($x->gets(), false);
    $t->assertFalse($x->getc());
    $t->assertTrue($x->eof());
    $t->assertTrue($x->validateChecksum());
    $t->assertSame($x->getContent(), null);
    $t->assertSame($x->getLinkname(), "env.v7/HLink_long");
    $t->assertSame($archive->find("env.v7/HLink_long")->getMTime(), $x->getMTime());

    $dir1 = $archive->find("env.v7/mode555/");
    $t->assertNotEmpty($dir1);
    $t->assertSame($dir1->getName(), "env.v7/mode555/");
    $t->assertSame($dir1->getSize(), 0);
    $t->assertSame($dir1->getMTime(), strtotime("1992:06:23 14:12:00"));
    $t->assertSame($dir1->getMode(), 0555);
    $t->assertSame($dir1->getUserId(), posix_getuid());
    $t->assertSame($dir1->getGroupId(), posix_getgid());
    $t->assertFalse($dir1->getc());
    $t->assertSame($dir1->read(20), "");
    $t->assertSame($dir1->gets(), false);
    $t->assertFalse($dir1->getc());
    $t->assertSame($dir1->length(), 0);
    $t->assertTrue($dir1->eof());
    $t->assertTrue($dir1->validateChecksum());
    $t->assertSame($dir1->getContent(), null);
    $t->assertSame($dir1->getLinkname(), "");

    #TODO more test

    fclose($fHandle);
    Utils::RM_TMP_FILES();
    Utils::RM_RF(sys_get_temp_dir() . DIRECTORY_SEPARATOR . ENV_NAME);
});

$t->test('Test phtar\v7\Archive test a simple find(...)', function() use($t, $databox) {
    $filename = Utils::RUN_IN(function() {
                require ENV_CRATION_FILE;
                exec("bsdtar " . ARCHIVE_FORMART . " -cf " . ($f = Utils::TMP_FILE('Tar')) . " " . ENV_NAME);
                return $f;
            });
    $archive = new Archive(new FileHandle($fHandle = fopen($filename, "r")));


    $t->assertNotEmpty($x = $archive->find("env.v7/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/FILE.txt"));
    $t->assertSame($x->getName(), "env.v7/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/FILE.txt");

    fclose($fHandle);
    Utils::RM_TMP_FILES();
    Utils::RM_RF(sys_get_temp_dir() . DIRECTORY_SEPARATOR . ENV_NAME);
});

$t->test('Test phtar\v7\Archive if file is a hard link', function() use($t, $databox) {
    $filename = Utils::RUN_IN(function() {
                require ENV_CRATION_FILE;
                exec("bsdtar " . ARCHIVE_FORMART . " -cf " . ($f = Utils::TMP_FILE('Tar')) . " " . ENV_NAME);
                return $f;
            });
    $archive = new Archive(new FileHandle($fHandle = fopen($filename, "r")));

    $t->assertNotEmpty($x = $archive->find("env.v7/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/FILE.txt"));

    $t->assertSame($x->getLinkname(), "env.v7/HLink_long");
    $t->assertSame($x->getType(), Archive::ENTRY_TYPE_HARDLINK);

    fclose($fHandle);
    Utils::RM_TMP_FILES();
    Utils::RM_RF(sys_get_temp_dir() . DIRECTORY_SEPARATOR . ENV_NAME);
});

$t->test('Test phtar\v7\Archive if file is a symbolic link', function() use($t, $databox) {
    $filename = Utils::RUN_IN(function() {
                require ENV_CRATION_FILE;
                exec("bsdtar " . ARCHIVE_FORMART . " -cf " . ($f = Utils::TMP_FILE('Tar')) . " " . ENV_NAME);
                return $f;
            });
    $archive = new Archive(new FileHandle($fHandle = fopen($filename, "r")));

    $t->assertNotEmpty($x = $archive->find("env.v7/SLink_long"));

    $t->assertSame($x->getLinkname(), str_repeat("this_is_a_long_dir/", 4) . "FILE.txt");
    $t->assertSame($x->getType(), Archive::ENTRY_TYPE_SOFTLINK);
    fclose($fHandle);
    Utils::RM_TMP_FILES();
    Utils::RM_RF(sys_get_temp_dir() . DIRECTORY_SEPARATOR . ENV_NAME);
});



$t->test('Test phtar\v7\Archive find() a symlink with its contents if target exists', function() use($t, $databox) {
    $filename = Utils::RUN_IN(function() {
                require ENV_CRATION_FILE;
                exec("bsdtar " . ARCHIVE_FORMART . " -cf " . ($f = Utils::TMP_FILE('Tar')) . " " . ENV_NAME);
                return $f;
            });
    $archive = new Archive(new FileHandle($fHandle = fopen($filename, "r")));


    $t->assertNotEmpty($x = $archive->find("env.v7/SLink_long"));
    $t->assertSame($x->getContent(), null);

    fclose($fHandle);
    Utils::RM_TMP_FILES();
    Utils::RM_RF(sys_get_temp_dir() . DIRECTORY_SEPARATOR . ENV_NAME);
});

$t->run();





























