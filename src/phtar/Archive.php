<?php

require_once __DIR__ . '/../Autoload.php';




if (false) {
    $a = new phtar\posixUs\Archive(new \phtar\utils\FileHandleReader(fopen($argv[1], "r+")));
    $a->buildIndex();
    $c = [];
    var_dump($a->validate());
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
} elseif (true) {



    $a = new phtar\gnu\Archive(new \phtar\utils\FileHandleReader(fopen($argv[1], "r+")));
    $a->buildIndex();
    $c = [];
    var_dump($a->validate());
    foreach ($a as $key => $value) {
        $value instanceof phtar\gnu\Entry;
        echo $key . PHP_EOL;
        #var_dump($value->getName());
    }
#var_dump($c);
#$f = fopen($argv[1], "r");
#var_dump(ftell($f));
#var_dump(fgetc($f));
#var_dump(ftell($f));
}
