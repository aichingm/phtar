<?php

use \Pest\Utils;
use \phtar\posix\Archive;
use \phtar\utils\FileHandle;

$t = new Pest\Pest(substr(basename(__FILE__), 0, -4));
$databox = new stdClass();
require_once __DIR__ . '/../src/Autoload.php';
define("ARCHIVE_FORMART", "--format=ustar");
define("ENV_CRATION_FILE", __DIR__ . '/assets/setup.env.posix.php');
define("ENV_NAME", 'env.posix');

$t->test('Test if the phtar\posix\Archive contains all files', function() use($t, $databox) {
    $filename = Utils::RUN_IN(function() {
                require ENV_CRATION_FILE;
                exec("bsdtar " . ARCHIVE_FORMART . " -cf " . ($f = Utils::TMP_FILE('Tar')) . " " . ENV_NAME);
                return $f;
            });
    $archive = new Archive(new FileHandle($fHandle = fopen($filename, "r")));

    $t->assertEquals(filesize($filename) % 512, 0);
//18 * 512 headers
//2*512 empty end blocks
// 3 * 512 content
// 5632 content
    $t->assertEquals(filesize($filename), 18 * 512 + 512 + 2 * 512 * 3 + 5632);

    $filelist = array(
        "env.posix/",
        "env.posix/mode777/",
        "env.posix/mode555/",
        "env.posix/dir/",
        "env.posix/dir/in/",
        "env.posix/dir/in/dir/",
        "env.posix/mode755/",
        "env.posix/mode755/A.txt",
        "env.posix/mode755/AB.txt",
        "env.posix/mode755/B.txt",
        "env.posix/mode755/HLink.B",
        "env.posix/mode755/SLink.B",
        "env.posix/" . str_repeat("A", 40) . "/",
        "env.posix/" . str_repeat("A", 40) . "/" . str_repeat("B", 48) . "/",
        "env.posix/" . str_repeat("A", 40) . "/" . str_repeat("B", 48) . "/" . str_repeat("C", 50) . "/",
        "env.posix/" . str_repeat("A", 40) . "/" . str_repeat("B", 48) . "/" . str_repeat("C", 50) . "/" . str_repeat("D", 50) . "/",
        "env.posix/" . str_repeat("A", 40) . "/" . str_repeat("B", 48) . "/" . str_repeat("C", 50) . "/" . str_repeat("D", 50) . "/" . str_repeat("E", 45) . "/",
        "env.posix/" . str_repeat("A", 40) . "/" . str_repeat("B", 48) . "/" . str_repeat("C", 50) . "/" . str_repeat("D", 50) . "/" . str_repeat("E", 45) . "/FFF",
    );

//test if all files are recognised 
    $size = 0;
    foreach ($archive as $file) {
        $size++;
    }
    $t->assertEquals($size, count($filelist));
    var_dump($archive->getIndex());
    $t->assertEquals(count($archive->getIndex()), count($filelist));
    $t->assertEquals(count($archive->getIndex()), 18);
//test if all files from the list are present
    foreach ($archive as $name => $file) {
        $pos = array_search($name, $filelist);
        if ($pos !== false) {
            unset($filelist[$pos]);
        } else {
            echo "$name NOT FOUND" . PHP_EOL;
        }
    }
    $t->assertEmpty($filelist);
    fclose($fHandle);
    echo $filename;
//Utils::RM_TMP_FILES();
    Utils::RM_RF(sys_get_temp_dir() . DIRECTORY_SEPARATOR . ENV_NAME);
});


$t->test('Test if the phtar\posix\Archive contains correct files and directories', function() use($t, $databox) {
    $filename = Utils::RUN_IN(function() {
                require ENV_CRATION_FILE;
                exec("bsdtar " . ARCHIVE_FORMART . " -cf " . ($f = Utils::TMP_FILE('Tar')) . " " . ENV_NAME);
                return $f;
            });
    $archive = new Archive(new FileHandle($fHandle = fopen($filename, "r")));


    $dirName = "env.posix/" . str_repeat("A", 40) . "/" . str_repeat("B", 48) . "/" . str_repeat("C", 50) . "/" . str_repeat("D", 50) . "/" . str_repeat("E", 45);
    $FFF = $archive->find($dirName . "/FFF");


    $t->assertSame($FFF->getName(), $dirName . "/FFF");
    $t->assertEquals($FFF->getType(), Archive::ENTRY_TYPE_FILE);
    $t->assertSame($FFF->getSize(), 80);
    $t->assertSame($FFF->getMTime(), strtotime("1992:06:23 14:12:00"));
    $t->assertSame($FFF->getMode(), 0644);
    $t->assertSame($FFF->getUserId(), posix_getuid());
    $t->assertSame($FFF->getGroupId(), posix_getgid());
    $t->assertSame($FFF->getUserName(), posix_getpwuid(posix_getuid())["name"]);
    $t->assertSame($FFF->getGroupName(), posix_getgrgid(posix_getuid())["name"]);
    $t->assertSame($FFF->getDevMajor(), 0);
    $t->assertSame($FFF->getDevMinor(), 0);
    $t->assertSame($FFF->getc(), "C");
    $t->assertSame($FFF->read(20), substr(str_repeat("CONTENT_", 10), 1, 20));
    $FFF->seek(0);
    $t->assertSame($FFF->gets(), str_repeat("CONTENT_", 10));
    $t->assertFalse($FFF->getc());
    $t->assertTrue($FFF->eof());
    $t->assertTrue($FFF->validateChecksum());
    $t->assertSame($FFF->getContent(), str_repeat("CONTENT_", 10));
    $t->assertSame($FFF->getLinkname(), "");

    $dir1 = $archive->find("env.posix/mode555/");
    $t->assertNotEmpty($dir1);
    $t->assertSame($dir1->getName(), "env.posix/mode555/");
    $t->assertSame($dir1->getSize(), 0);
    $t->assertSame($dir1->getMTime(), strtotime("1992:06:23 14:12:00"));
    $t->assertSame($dir1->getMode(), 0555);
    $t->assertSame($dir1->getUserId(), posix_getuid());
    $t->assertSame($dir1->getGroupId(), posix_getgid());
    $t->assertSame($dir1->getUserName(), posix_getpwuid(posix_getuid())["name"]);
    $t->assertSame($dir1->getGroupName(), posix_getgrgid(posix_getuid())["name"]);
    $t->assertSame($dir1->getDevMajor(), 0);
    $t->assertSame($dir1->getDevMinor(), 0);
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

$t->test('Test phtar\posix\Archive test a simple find(...)', function() use($t, $databox) {
    $filename = Utils::RUN_IN(function() {
                require ENV_CRATION_FILE;
                exec("bsdtar " . ARCHIVE_FORMART . " -cf " . ($f = Utils::TMP_FILE('Tar')) . " " . ENV_NAME);
                return $f;
            });
    $archive = new Archive(new FileHandle($fHandle = fopen($filename, "r")));

    $dirName = "env.posix/" . str_repeat("A", 40) . "/" . str_repeat("B", 48) . "/" . str_repeat("C", 50) . "/" . str_repeat("D", 50) . "/" . str_repeat("E", 45);
    $filename = $dirName . "/FFF";
    $t->assertNotEmpty($x = $archive->find($filename));
    $t->assertSame($x->getName(), $filename);

    $t->assertNotEmpty($x1 = $archive->find("env.posix/mode755/HLink.B"));
    $t->assertSame($x1->getName(), "env.posix/mode755/HLink.B");

    $t->assertNotEmpty($x2 = $archive->find("env.posix/" . str_repeat("A", 40) . "/"));
    $t->assertSame($x2->getName(), "env.posix/" . str_repeat("A", 40) . "/");

    fclose($fHandle);
    Utils::RM_TMP_FILES();
    Utils::RM_RF(sys_get_temp_dir() . DIRECTORY_SEPARATOR . ENV_NAME);
});

$t->test('Test phtar\posix\Archive if file is a hard link', function() use($t, $databox) {
    $filename = Utils::RUN_IN(function() {
                require ENV_CRATION_FILE;
                exec("bsdtar " . ARCHIVE_FORMART . " -cf " . ($f = Utils::TMP_FILE('Tar')) . " " . ENV_NAME);
                return $f;
            });
    $archive = new Archive(new FileHandle($fHandle = fopen($filename, "r")));

    $t->assertNotEmpty($x = $archive->find("env.posix/mode755/B.txt"));

    $t->assertEquals($x->getLinkname(), "env.posix/mode755/HLink.B");
    $t->assertEquals($x->getType(), Archive::ENTRY_TYPE_HARDLINK);

    fclose($fHandle);
    Utils::RM_TMP_FILES();
    Utils::RM_RF(sys_get_temp_dir() . DIRECTORY_SEPARATOR . ENV_NAME);
});
$t->test('Test phtar\posix\Archive if file is a symbolic link', function() use($t, $databox) {
    $filename = Utils::RUN_IN(function() {
                require ENV_CRATION_FILE;
                exec("bsdtar " . ARCHIVE_FORMART . " -cf " . ($f = Utils::TMP_FILE('Tar')) . " " . ENV_NAME);
                return $f;
            });
    $archive = new Archive(new FileHandle($fHandle = fopen($filename, "r")));

    $t->assertNotEmpty($x = $archive->find("env.posix/mode755/SLink.B"));

    $t->assertEquals($x->getLinkname(), "B.txt");
    $t->assertEquals($x->getType(), Archive::ENTRY_TYPE_SOFTLINK);
    fclose($fHandle);
    Utils::RM_TMP_FILES();
    Utils::RM_RF(sys_get_temp_dir() . DIRECTORY_SEPARATOR . ENV_NAME);
});

$t->test('Test phtar\posix\Archive test a simple find(...)', function() use($t, $databox) {
    $filename = Utils::RUN_IN(function() {
                require ENV_CRATION_FILE;
                exec("bsdtar " . ARCHIVE_FORMART . " -cf " . ($f = Utils::TMP_FILE('Tar')) . " " . ENV_NAME);
                return $f;
            });
    $archive = new Archive(new FileHandle($fHandle = fopen($filename, "r")));


    $t->assertNotEmpty($x = $archive->find("env.posix/mode755/SLink.B"));
    $t->assertSame($x->getContent(), null);

    fclose($fHandle);
    Utils::RM_TMP_FILES();
    Utils::RM_RF(sys_get_temp_dir() . DIRECTORY_SEPARATOR . ENV_NAME);
});

$t->run();





























