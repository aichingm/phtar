<?php

/**
 * This file is part of Phtar
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
use \Pest\Utils;
use \phtar\gnu\Archive;
use \phtar\utils\FileHandle;

$t = new Pest\Pest(substr(basename(__FILE__), 0, -4));
$databox = new stdClass();
require_once __DIR__ . '/../src/Autoload.php';
define("ARCHIVE_FORMART", "--format=gnutar");
define("ENV_CRATION_FILE", __DIR__ . '/assets/setup.env.gnu.php');
define("ENV_NAME", 'env.gnu');

$t->test('Test if the phtar\gnu\Archive contains all files', function() use($t, $databox) {
    $filename = Utils::RUN_IN(function() {
                require ENV_CRATION_FILE;
                exec("bsdtar " . ARCHIVE_FORMART . " -cf " . ($f = Utils::TMP_FILE('Tar')) . " " . ENV_NAME);
                return $f;
            });
    $archive = new Archive(new FileHandle($fHandle = fopen($filename, "r")));

    $t->assertEquals(filesize($filename) % 512, 0);

    $t->assertEquals(filesize($filename), 59392 + (8 * 512));


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
        "env.gnu/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/FILE.txt",
        "env.gnu/" . str_repeat("this_is_an_extreme_long_directory/", 1),
        "env.gnu/" . str_repeat("this_is_an_extreme_long_directory/", 2),
        "env.gnu/" . str_repeat("this_is_an_extreme_long_directory/", 3),
        "env.gnu/" . str_repeat("this_is_an_extreme_long_directory/", 4),
        "env.gnu/" . str_repeat("this_is_an_extreme_long_directory/", 5),
        "env.gnu/" . str_repeat("this_is_an_extreme_long_directory/", 6),
        "env.gnu/" . str_repeat("this_is_an_extreme_long_directory/", 7),
        "env.gnu/" . str_repeat("this_is_an_extreme_long_directory/", 8),
        "env.gnu/" . str_repeat("this_is_an_extreme_long_directory/", 9),
        "env.gnu/" . str_repeat("this_is_an_extreme_long_directory/", 10),
        "env.gnu/" . str_repeat("this_is_an_extreme_long_directory/", 11),
        "env.gnu/" . str_repeat("this_is_an_extreme_long_directory/", 12),
        "env.gnu/" . str_repeat("this_is_an_extreme_long_directory/", 13),
        "env.gnu/" . str_repeat("this_is_an_extreme_long_directory/", 14),
        "env.gnu/" . str_repeat("this_is_an_extreme_long_directory/", 15),
        "env.gnu/" . str_repeat("this_is_an_extreme_long_directory/", 16),
        "env.gnu/" . str_repeat("this_is_an_extreme_long_directory/", 17),
        "env.gnu/" . str_repeat("this_is_an_extreme_long_directory/", 18),
        "env.gnu/" . str_repeat("this_is_an_extreme_long_directory/", 19),
        "env.gnu/" . str_repeat("this_is_an_extreme_long_directory/", 20),
        "env.gnu/" . str_repeat("this_is_an_extreme_long_directory/", 20) . "Extrem_Long_File",
        "env.gnu/" . str_repeat("oOoO_", 25) . ".link",
        "env.gnu/" . str_repeat("xXxX_", 25) . ".file"
    );

//test if all files are recognised 
    $size = 0;
    foreach ($archive as $file) {
        $size++;
    }
    $t->assertEquals($size, count($filelist));
    $t->assertEquals(count($archive->getIndex()), count($filelist));
//test if al files from the list are present
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

    $EMPTY_SPARSE_LIST = array(
        0 =>
        array(
            'offset' => 0,
            'numbytes' => 0,
        ),
        1 =>
        array(
            'offset' => 0,
            'numbytes' => 0,
        ),
        2 =>
        array(
            'offset' => 0,
            'numbytes' => 0,
        ),
        3 =>
        array(
            'offset' => 0,
            'numbytes' => 0,
        )
    );



    $x = $archive->find("env.gnu/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/FILE.txt");

    $t->assertSame($x->getName(), "env.gnu/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/FILE.txt");
    $t->assertEquals($x->getType(), Archive::ENTRY_TYPE_HARDLINK);
    $t->assertSame($x->getSize(), 0);
    $t->assertSame($x->getMTime(), strtotime("1992:06:23 14:12:00"));
    $t->assertSame($x->getMode(), 0755);
    $t->assertSame($x->getUserId(), posix_getuid());
    $t->assertSame($x->getGroupId(), posix_getgid());
    $t->assertSame($x->getUserName(), posix_getpwuid(posix_getuid())["name"]);
    $t->assertSame($x->getGroupName(), posix_getgrgid(posix_getuid())["name"]);
    $t->assertSame($x->getDevMajor(), 0);
    $t->assertSame($x->getDevMinor(), 0);
    echo "This is a bug in bsdtar. Line: " . ( __LINE__ + 1) . PHP_EOL;
#$t->assertSame($x->getATime(), strtotime("1992:06:23 14:12:00"));
    echo "This is a bug in bsdtar. Line: " . (__LINE__ + 1) . PHP_EOL;
#$t->assertSame($x->getCTime(), strtotime("1992:06:23 14:12:00"));
    $t->assertSame($x->getOffset(), 0);
    $t->assertSame($x->getLongnames(), str_repeat("\0", 4));
    $t->assertSame($x->getSparseList(), $EMPTY_SPARSE_LIST);
    $t->assertSame($x->isExtended(), false);
    $t->assertSame($x->getRealSize(), 0);


    $t->assertFalse($x->getc());
    $t->assertSame($x->read(20), "");
    $t->assertSame($x->gets(), false);
    $t->assertFalse($x->getc());
    $t->assertTrue($x->eof());
    $t->assertTrue($x->validateChecksum());
    $t->assertSame($x->getContent(), null, "This header if of the type HARD_LINK");
    $t->assertSame($x->getLinkname(), "env.gnu/HLink_long");
    $t->assertSame($archive->find("env.gnu/HLink_long")->getMTime(), $x->getMTime());

    $dir1 = $archive->find("env.gnu/mode555/");
    $t->assertNotEmpty($dir1);
    $t->assertSame($dir1->getName(), "env.gnu/mode555/");
    $t->assertSame($dir1->getSize(), 0);
    $t->assertSame($dir1->getMTime(), strtotime("1992:06:23 14:12:00"));
    $t->assertSame($dir1->getMode(), 0555);
    $t->assertSame($dir1->getUserId(), posix_getuid());
    $t->assertSame($dir1->getGroupId(), posix_getgid());
    $t->assertSame($dir1->getUserName(), posix_getpwuid(posix_getuid())["name"]);
    $t->assertSame($dir1->getGroupName(), posix_getgrgid(posix_getuid())["name"]);
    $t->assertSame($dir1->getDevMajor(), 0);
    $t->assertSame($dir1->getDevMinor(), 0);
    echo "This is a bug in bsdtar. Line: " . (__LINE__ + 1) . PHP_EOL;
#$t->assertSame$dir1x->getATime(), strtotime("1992:06:23 14:12:00"));
    echo "This is a bug in bsdtar. Line: " . (__LINE__ + 1) . PHP_EOL;
#$t->assertSame$dir1x->getCTime(), strtotime("1992:06:23 14:12:00"));
    $t->assertSame($dir1->getOffset(), 0);
    $t->assertSame($dir1->getLongnames(), str_repeat("\0", 4));
    $t->assertSame($dir1->getSparseList(), $EMPTY_SPARSE_LIST);
    $t->assertSame($dir1->isExtended(), false);
    $t->assertSame($dir1->getRealSize(), 0);
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

$t->test('Test phtar\gnu\Archive test a simple find(...)', function() use($t, $databox) {
    $filename = Utils::RUN_IN(function() {
                require ENV_CRATION_FILE;
                exec("bsdtar " . ARCHIVE_FORMART . " -cf " . ($f = Utils::TMP_FILE('Tar')) . " " . ENV_NAME);
                return $f;
            });
    $archive = new Archive(new FileHandle($fHandle = fopen($filename, "r")));


    $t->assertNotEmpty($x = $archive->find("env.gnu/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/FILE.txt"));
    $t->assertSame($x->getName(), "env.gnu/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/FILE.txt");

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

$t->test('Test phtar\gnu\Archive long link names (longer than 100 bytes)', function()use($t) {
    $filename = Utils::RUN_IN(function() {
                mkdir(ENV_NAME);
                chdir(ENV_NAME);
                touch(str_repeat("oOoO_", 25) . ".link");
                link(str_repeat("oOoO_", 25) . ".link", str_repeat("xXxX_", 25) . ".file");
                chdir("..");
                exec("bsdtar " . ARCHIVE_FORMART . " -cf " . ($f = Utils::TMP_FILE('Tar')) . " " . ENV_NAME);
                return $f;
            });
    $archive = new Archive(new FileHandle($fHandle = fopen($filename, "r")));


    $t->assertNotEmpty($x = $archive->find(ENV_NAME . "/" . str_repeat("xXxX_", 25) . ".file"));
    $t->assertSame($x->getType(), Archive::ENTRY_TYPE_FILE);
    $t->assertSame($x->getName(), ENV_NAME . "/" . str_repeat("xXxX_", 25) . ".file");

    $t->assertNotEmpty($y = $archive->find(ENV_NAME . "/" . str_repeat("oOoO_", 25) . ".link"));
    $t->assertSame($y->getType(), Archive::ENTRY_TYPE_HARDLINK);
    $t->assertSame($y->getLinkname(), ENV_NAME . "/" . str_repeat("xXxX_", 25) . ".file");
    fclose($fHandle);
    Utils::RM_TMP_FILES();
    Utils::RM_RF(sys_get_temp_dir() . DIRECTORY_SEPARATOR . ENV_NAME);
});



$t->run();





























