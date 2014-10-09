<?php
require_once '../../../../require.php';
\maLib\init\Requirer::enableAutoClassLoading();

$tac = new phtar\TarArchiveCreator(new maLib\lib\filephtarBlockFactory, new \phtar\utils\ContentFixed512Factory(), new maLib\lib\files\tar\GnuUSTar\TarArchive);
chdir("..");
$tac->addDirectory("utils/");
$tac->addFile("utils/MetaBlockAnalyser.php");
$tac->addFile("utils/EmptyBlockFactory.php");
$tac->addFile("utils/ContentFixed512Factory.php");
file_put_contents("tests/posixustar.tar", $tac."");



