<?php

/**
 * This file is part of Phtar
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
$t = new Pest\Pest(substr(basename(__FILE__), 0, -4));
$databox = new stdClass();
require_once __DIR__ . '/../src/Autoload.php';

use \Pest\Utils;
use \phtar\utils\FileHandle;
use phtar\utils\DirectoryArchiver;

$t->test("Test DirectoryArchiver with root dir", function() use($t, $databox) {
    $ENV_NAME = 'env.v7';
    $ENV_CRATION_FILE = __DIR__ . '/assets/setup.env.v7.php';


    Utils::RUN_IN(function() use($ENV_CRATION_FILE) {
        require $ENV_CRATION_FILE;
    });
    //create file
    $filename = Utils::TMP_FILE("Arc");
    //open file handle
    $fh = new FileHandle($fHandle = fopen($filename, "r+"));
    //create archive creator
    $ac = new \phtar\v7\ArchiveCreator($fh);
    //create DirectoryArchiver pass ArchiveCreator and directory
    $directoryArchiver = new DirectoryArchiver($ac, sys_get_temp_dir() . DIRECTORY_SEPARATOR . $ENV_NAME);
    //archive directory
    $directoryArchiver->archive();
    //tell the ArchiveCreator to write the contents to the file handle
    $ac->write();
    //test if the writen archive is a valid tar archive
    $t->assertSame(system("/usr/bin/file $filename"), "$filename: tar archive", "test the archive with the 'file' utility");

    $files = array(
        "./env.v7/",
        "./env.v7/SLink_long",
        "./env.v7/HLink_long",
        "./env.v7/this_is_a_long_dir/",
        "./env.v7/this_is_a_long_dir/this_is_a_long_dir/",
        "./env.v7/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/",
        "./env.v7/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/",
        "./env.v7/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/FILE.txt",
        "./env.v7/mode755/",
        "./env.v7/mode755/CMtime.txt",
        "./env.v7/mode755/SLink.B",
        "./env.v7/mode755/HLink.B",
        "./env.v7/mode755/B.txt",
        "./env.v7/mode755/AB.txt",
        "./env.v7/mode755/A.txt",
        "./env.v7/dir/",
        "./env.v7/dir/in/",
        "./env.v7/dir/in/dir/",
        "./env.v7/dir/in/dir/CMtime.txt",
        "./env.v7/mode555/",
        "./env.v7/mode777/"
    );


    $output1 = array();
    exec("bsdtar -tf $filename", $output1);

    $t->assertSame(count($output1), 21, "test with bsdtar");

    $output2 = array();
    exec("tar -tf $filename", $output2);
    $t->assertSame(count($output2), 21, "test with tar");
    foreach ($output2 as $file) {
        $t->assertSame(strpos($file, "./env.v7/"), 0);
        $t->assertTrue(in_array($file, $files));
    }


    fclose($fHandle);
    Utils::RM_RF(sys_get_temp_dir() . DIRECTORY_SEPARATOR . $ENV_NAME);
});

$t->test("Test DirectoryArchiver without root dir", function() use($t, $databox) {
    $ENV_NAME = 'env.v7';
    $ENV_CRATION_FILE = __DIR__ . '/assets/setup.env.v7.php';


    Utils::RUN_IN(function() use($ENV_CRATION_FILE) {
        require $ENV_CRATION_FILE;
    });
    //create file
    $filename = Utils::TMP_FILE("Arc");
    //open file handle
    $fh = new FileHandle($fHandle = fopen($filename, "r+"));
    //create archive creator
    $ac = new \phtar\v7\ArchiveCreator($fh);
    //create DirectoryArchiver pass ArchiveCreator and directory
    $directoryArchiver = new DirectoryArchiver($ac, sys_get_temp_dir() . DIRECTORY_SEPARATOR . $ENV_NAME);
    //set the without root dir flag
    $directoryArchiver->withoutRootDir();
    //archive directory
    $directoryArchiver->archive();
    //tell the ArchiveCreator to write the contents to the file handle
    $ac->write();
    //test if the writen archive is a valid tar archive
    $t->assertSame(system("/usr/bin/file $filename"), "$filename: tar archive", "test the archive with the 'file' utility");

    $files = array(
        "./",
        "./SLink_long",
        "./HLink_long",
        "./this_is_a_long_dir/",
        "./this_is_a_long_dir/this_is_a_long_dir/",
        "./this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/",
        "./this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/",
        "./this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/FILE.txt",
        "./mode755/",
        "./mode755/CMtime.txt",
        "./mode755/SLink.B",
        "./mode755/HLink.B",
        "./mode755/B.txt",
        "./mode755/AB.txt",
        "./mode755/A.txt",
        "./dir/",
        "./dir/in/",
        "./dir/in/dir/",
        "./dir/in/dir/CMtime.txt",
        "./mode555/",
        "./mode777/"
    );


    $output1 = array();
    exec("bsdtar -tf $filename", $output1);

    $t->assertSame(count($output1), 21, "test with bsdtar");

    $output2 = array();
    exec("tar -tf $filename", $output2);
    $t->assertSame(count($output2), 21, "test with tar");
    foreach ($output2 as $file) {
        $t->assertSame(strpos($file, "./"), 0);
        $t->assertTrue(in_array($file, $files), $file);
    }


    fclose($fHandle);
    Utils::RM_RF(sys_get_temp_dir() . DIRECTORY_SEPARATOR . $ENV_NAME);
});




$t->run();

