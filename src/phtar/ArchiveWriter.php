<?php

require_once __DIR__ . '/../Autoload.php';





$a = new phtar\v7\Archive(new \phtar\utils\FileHandleReader(fopen($argv[1], "r+")));
$a->buildIndex();
$c = [];
foreach ($a as $key => $value) {
    //$c[] = clone $value;
    //$value->setRaw(true);
    #if (strpos($key, "XYZ") !== false) {
    var_dump($key, $value, phtar\utils\ArchiveType::entryType($value->getHeaderHandle()), $value->getMode(), $value->getGroupId());
    #}
    #$value->seek(0);
    var_dump($value->getMode(), $value->read(512));
}
#var_dump($c);
#$f = fopen($argv[1], "r");
#var_dump(ftell($f));
#var_dump(fgetc($f));
#var_dump(ftell($f));
