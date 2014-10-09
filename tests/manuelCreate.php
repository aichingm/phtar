<?php

require_once '../src/Autoload.php';
$tac = new \phtar\TarArchiveCreator(new phtar\PosixUSTar\TarChunkFactory, new phtar\utils\ContentFixed512Factory(), new \phtar\TarArchive());
$tac->add("assets/");
$tac->add("assets/.gitignore");
$tac->add("assets/LICENSE");
$tac->add("assets/README.md");
$tac->add("assets/notes");
file_put_contents("posixustar.tar", $tac . "");
$tac = new \phtar\TarArchiveCreator(new phtar\GnuOldTar\TarChunkFactory, new phtar\utils\ContentFixed512Factory(), new \phtar\TarArchive());

$tac->add("assets/");
$tac->add("assets/.gitignore");
$tac->add("assets/LICENSE");
$tac->add("assets/README.md");
$tac->add("assets/notes");
file_put_contents("gnu.tar", $tac . "");

$tac = new \phtar\TarArchiveCreator(new phtar\GnuUSTar\TarChunkFactory, new phtar\utils\ContentFixed512Factory(), new \phtar\TarArchive());

$tac->add("assets/");
$tac->add("assets/.gitignore");
$tac->add("assets/LICENSE");
$tac->add("assets/README.md");
$tac->add("assets/notes");
file_put_contents("gnuus.tar", $tac . "");