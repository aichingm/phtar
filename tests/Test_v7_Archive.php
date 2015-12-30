<?php

$t = new Pest\Pest(substr(basename(__FILE__), 0, -4));
$databox = new stdClass();
require_once __DIR__ . '/../src/Autoload.php';


$t->test('Test phtar\v7\Archive', function() use($t, $databox) {
    $cwd = getcwd();
    chdir(sys_get_temp_dir());
    require __DIR__ . '/assets/setup.env.v7.php';
    $filename = tempnam(sys_get_temp_dir(), 'Tar');
    exec("bsdtar --format=v7 -cvf  $filename " . ENV_NAME);
    chdir($cwd);
    $fHandle = fopen($filename, "r");
    $handle = new \phtar\utils\FileHandle($fHandle);


    $filelist = array(
        "env.v7/",
        "env.v7/SLink_long",
        "env.v7/HLink_long",
        "env.v7/this_is_a_long_dir/",
        "env.v7/mode755/",
        "env.v7/dir/",
        "env.v7/mode644/",
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


    $archive = new \phtar\v7\Archive($handle);
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
    
    $x = $archive->find("env.v7/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/FILE.txt");
    $t->assertEquals($x->getName(), "env.v7/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/this_is_a_long_dir/FILE.txt");


    #TODO more test
    
    fclose($fHandle);
    #TODO remove //
    //unlink($filename);
    Pest\Utils::RM_RF(sys_get_temp_dir() . DIRECTORY_SEPARATOR . ENV_NAME);
});


$t->run();





























