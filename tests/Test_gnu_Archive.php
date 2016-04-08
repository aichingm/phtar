<?php

use \Pest\Utils;
use \phtar\gnu\Archive;
use \phtar\utils\FileHandle;

$t = new Pest\Pest(substr(basename(__FILE__), 0, -4));
$databox = new stdClass();
require_once __DIR__ . '/../src/Autoload.php';
define("ARCHIVE_FORMART", "--format=gnutar");
define("ENV_CRATION_FILE", __DIR__ . '/assets/setup.env.gnu.php');
$t->test('Test if the phtar\gnu\Archive contains all files', function() use($t, $databox) {
    $filename = Utils::RUN_IN(function() {
                require ENV_CRATION_FILE;
                exec("bsdtar " . ARCHIVE_FORMART . " -cf " . ($f = Utils::TMP_FILE('Tar')) . " " . ENV_NAME);
                return $f;
            });
    $archive = new Archive(new FileHandle($fHandle = fopen($filename, "r")));

    $t->assertEquals(filesize($filename) % 512, 0);

    $t->assertEquals(filesize($filename), 25600);


    $filelist = array(
        "env.gnu/",
        "env.gnu/SLink_long",
        "env.gnu/HLink_long",
        "env.gnu/this_is_a_long_dir/",
        "env.gnu/mode755/",
        "env.gnu/dir/",
        "env.gnu/mode555/",
        "env.gnu/mode777/",
        "env.gnu/dir/in/",
        "env.gnu/dir/in/dir/",
        "env.gnu/dir/in/dir/CMtime.txt",
        "env.gnu/mode755/CMtime.txt",
        "env.gnu/mode755/SLink.B",
        "env.gnu/mode755/HLink.B",
        "env.gnu/mode755/B.txt",
        "env.gnu/mode755/AB.txt",
        "env.gnu/mode755/A.txt",
        "env.gnu/this_is_a_long_dir/this_is_a_long_dir/",
        "env.gnu/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/",
        "env.gnu/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/",
        "env.gnu/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/FILE.txt"
    );

    //test if all files are recognised 
    $size = 0;
    foreach ($archive as $file) {
        $size++;
    }
    $t->assertEquals($size, count($filelist));
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


$t->test('Test if the phtar\gnu\Archive contains correct files and directories', function() use($t, $databox) {
    $filename = Utils::RUN_IN(function() {
                require ENV_CRATION_FILE;
                exec("bsdtar " . ARCHIVE_FORMART . " -cf " . ($f = Utils::TMP_FILE('Tar')) . " " . ENV_NAME);
                return $f;
            });
    $archive = new Archive(new FileHandle($fHandle = fopen($filename, "r")));

    $x = $archive->find("env.gnu/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/FILE.txt");
    var_dump($archive->listEntries());
    $t->assertEquals($x->getName(), "env.gnu/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/FILE.txt");
    $t->assertEquals($x->getSize(), 0);
    $t->assertEquals($x->getMTime(), strtotime("1992:06:23 14:12:00"));
    $t->assertEquals($x->getMode(), 0755);
    $t->assertEquals($x->getUserId(), posix_getuid());
    $t->assertEquals($x->getGroupId(), posix_getgid());
    $t->assertFalse($x->getc());
    $t->assertEquals($x->read(20), "");
    $t->assertEquals($x->gets(), "");
    $t->assertFalse($x->getc());
    $t->assertTrue($x->eof());
    $t->assertTrue($x->validateChecksum());
    $t->assertEquals($x->getContent(), "");
    $t->assertEquals($x->getLinkname(), "env.gnu/HLink_long");
    $t->assertEquals($archive->find("env.gnu/HLink_long")->getMTime(), $x->getMTime());

    $dir1 = $archive->find("env.gnu/mode555/");
    $t->assertNotEmpty($dir1);
    $t->assertEquals($dir1->getName(), "env.gnu/mode555/");
    $t->assertEquals($dir1->getSize(), 0);
    $t->assertEquals($dir1->getMTime(), strtotime("1992:06:23 14:12:00"));
    $t->assertEquals($dir1->getMode(), 0555);
    $t->assertEquals($dir1->getUserId(), posix_getuid());
    $t->assertEquals($dir1->getGroupId(), posix_getgid());
    $t->assertFalse($dir1->getc());
    $t->assertEquals($dir1->read(20), "");
    $t->assertEquals($dir1->gets(), "");
    $t->assertFalse($dir1->getc());
    $t->assertEquals($dir1->length(), 0);
    $t->assertTrue($dir1->eof());
    $t->assertTrue($dir1->validateChecksum());
    $t->assertEquals($dir1->getContent(), "");
    $t->assertEquals($dir1->getLinkname(), "");

    #TODO more test

    fclose($fHandle);
    Utils::RM_TMP_FILES();
    Utils::RM_RF(sys_get_temp_dir() . DIRECTORY_SEPARATOR . ENV_NAME);
});

$t->test('Test phtar\gnu\Archive test a simple find(...)', function() use($t, $databox) {
    $filename = Utils::RUN_IN(function() {
                require ENV_CRATION_FILE;
                exec("bsdtar " . ARCHIVE_FORMART . " -cf " . ($f = Utils::TMP_FILE('Tar')) . " " . ENV_NAME);
                return $f;
            });
    $archive = new Archive(new FileHandle($fHandle = fopen($filename, "r")));


    $t->assertNotEmpty($x = $archive->find("env.gnu/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/FILE.txt"));
    $t->assertEquals($x->getName(), "env.gnu/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/FILE.txt");

    fclose($fHandle);
    Utils::RM_TMP_FILES();
    Utils::RM_RF(sys_get_temp_dir() . DIRECTORY_SEPARATOR . ENV_NAME);
});

$t->test('Test phtar\gnu\Archive if file is a hard link', function() use($t, $databox) {
    $filename = Utils::RUN_IN(function() {
                require ENV_CRATION_FILE;
                exec("bsdtar " . ARCHIVE_FORMART . " -cf " . ($f = Utils::TMP_FILE('Tar')) . " " . ENV_NAME);
                return $f;
            });
    $archive = new Archive(new FileHandle($fHandle = fopen($filename, "r")));

    $t->assertNotEmpty($x = $archive->find("env.gnu/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/FILE.txt"));

    $t->assertEquals($x->getLinkname(), "env.gnu/HLink_long");
    $t->assertEquals($x->getType(), Archive::ENTRY_TYPE_HARDLINK);

    fclose($fHandle);
    Utils::RM_TMP_FILES();
    Utils::RM_RF(sys_get_temp_dir() . DIRECTORY_SEPARATOR . ENV_NAME);
});

$t->test('Test phtar\gnu\Archive if file is a symbolic link', function() use($t, $databox) {
    $filename = Utils::RUN_IN(function() {
                require ENV_CRATION_FILE;
                exec("bsdtar " . ARCHIVE_FORMART . " -cf " . ($f = Utils::TMP_FILE('Tar')) . " " . ENV_NAME);
                return $f;
            });
    $archive = new Archive(new FileHandle($fHandle = fopen($filename, "r")));

    $t->assertNotEmpty($x = $archive->find("env.gnu/SLink_long"));

    $t->assertEquals($x->getLinkname(), str_repeat("this_is_a_long_dir/", 4) . "FILE.txt");
    $t->assertEquals($x->getType(), Archive::ENTRY_TYPE_SOFTLINK);
    fclose($fHandle);
    Utils::RM_TMP_FILES();
    Utils::RM_RF(sys_get_temp_dir() . DIRECTORY_SEPARATOR . ENV_NAME);
});

$t->test('Test phtar\gnu\Archive test a simple find(...)', function() use($t, $databox) {
    $filename = Utils::RUN_IN(function() {
                require ENV_CRATION_FILE;
                exec("bsdtar " . ARCHIVE_FORMART . " -cf " . ($f = Utils::TMP_FILE('Tar')) . " " . ENV_NAME);
                return $f;
            });
    $archive = new Archive(new FileHandle($fHandle = fopen($filename, "r")));


    $t->assertNotEmpty($x = $archive->find("env.gnu/SLink_long"));
    $t->assertSame($x->getContent(), null);

    fclose($fHandle);
    Utils::RM_TMP_FILES();
    Utils::RM_RF(sys_get_temp_dir() . DIRECTORY_SEPARATOR . ENV_NAME);
});

$t->run();





























