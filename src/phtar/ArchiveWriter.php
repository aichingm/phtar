<?php

require_once __DIR__ . '/../Autoload.php';

#$c = new \phtar\v7\ArchiveCreator(new phtar\utils\FileHandle(fopen($argv[1], "r+")));

file_put_contents($argv[1], "");

if (false) {
    $c = new \phtar\posixUs\ArchiveCreator(new phtar\utils\FileHandle(fopen($argv[1], "r+")));

    $c->add(new \phtar\posixUs\LinuxFsEntry("src/phtar/" . basename(__FILE__)));
    $c->add(new \phtar\posixUs\LinuxFsEntry("src/phtar/v7"));
    $c->add(new \phtar\posixUs\LinuxFsEntry("src/Autoload.php"));
    $c->add(new \phtar\posixUs\DirEntry("src/phtar/test1/"));
    $c->add(new \phtar\posixUs\BaseEntry("src/phtar/test/file.txt", "this is a test!"));
    $c->add(new \phtar\posixUs\BaseEntry("this/is/a/vary/super/extreme/long/name/for/a/directory/which/only/exist/to/test/if/the/archive/can/handle/file/names/zhis/long/test/file.txt", "this is a file with a long filename"));
}


if (false) {
    $c = new \phtar\posixUs\ArchiveCreator(new phtar\utils\FileHandle(fopen($argv[1], "r+")));

    $c->add(new \phtar\posixUs\LinuxFsEntry("src/phtar/" . basename(__FILE__)));
    $c->add(new \phtar\posixUs\LinuxFsEntry("src/phtar/v7"));
    $c->add(new \phtar\posixUs\LinuxFsEntry("src/Autoload.php"));
    $c->add(new \phtar\posixUs\DirEntry("src/phtar/test1/"));
    $c->add(new \phtar\posixUs\BaseEntry("src/phtar/test/file.txt", "this is a test!"));
    $c->add(new \phtar\posixUs\BaseEntry("this/is/a/vary/super/extreme/long/name/for/a/directory/which/only/exist/to/test/if/the/archive/can/handle/file/names/zhis/long/test/file.txt", "this is a file with a long filename"));
}

if (true) {
    $c = new \phtar\gnu\ArchiveCreator(new phtar\utils\FileHandle(fopen($argv[1], "r+")));

    $c->add(new \phtar\gnu\LinuxFsEntry("src/phtar/" . basename(__FILE__)));
    $c->add(new \phtar\gnu\LinuxFsEntry("src/phtar/v7"));
    $c->add(new \phtar\gnu\LinuxFsEntry("src/Autoload.php"));
    $c->add(new \phtar\gnu\DirEntry("src/phtar/test1/"));
    $c->add(new \phtar\gnu\BaseEntry("src/phtar/test/file.txt", "this is a test!"));
    $c->add(new \phtar\gnu\BaseEntry("this/is/a/vary/super/extreme/long/name/for/a/directory/which/only/exist/to/test/if/the/archive/can/handle/file/names/zhis/long/test/file.txt", "this is a file with a long filename"));
}




$c->write();
