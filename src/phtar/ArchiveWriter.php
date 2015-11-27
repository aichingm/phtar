<?php

require_once __DIR__ . '/../Autoload.php';

#$c = new \phtar\v7\ArchiveCreator(new phtar\utils\FileHandle(fopen($argv[1], "r+")));
$c = new \phtar\posixUs\ArchiveCreator(new phtar\utils\FileHandle(fopen($argv[1], "r+")));

$c->add(new \phtar\posixUs\LinuxFsEntry("src/Autoload.php"));
#$c->add(new \phtar\v7\LinuxFsEntry("src/phtar/".basename(__FILE__)));
#$c->add(new \phtar\v7\LinuxFsEntry("src/phtar/v7"));
#$c->add(new \phtar\v7\BaseEntry("src/phtar/test/file.txt", "this is a test!"));

$c->write();
