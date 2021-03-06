<?php

/**
 * This file is part of Phtar
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
$t = new Pest\Pest(substr(basename(__FILE__), 0, -4));
$databox = new stdClass();
require_once __DIR__ . '/../src/Autoload.php';

use \phtar\utils\LinuxFileHelper;

$t->test("Tests this file", function() use($t, $databox) {
    $t->assertEquals(LinuxFileHelper::MAJOR_MINOR(__FILE__)[0], 0, "Major");
    $t->assertEquals(LinuxFileHelper::MAJOR_MINOR(__FILE__)[1], 0, "Minor");
});

$t->test("Tests /dev/tty0", function() use($t, $databox) {
    $t->assertEquals(LinuxFileHelper::MAJOR_MINOR("/dev/tty0")[0], 4, "Major");
    $t->assertEquals(LinuxFileHelper::MAJOR_MINOR("/dev/tty0")[1], 0, "Minor");
});

$t->test("Tests /dev/sr0", function() use($t, $databox) {
    #$file = "/dev/sr0";
    $file = "/dev/sda";
    $t->assertEquals(LinuxFileHelper::MAJOR_MINOR($file)[0], 8, "Major");
    $t->assertEquals(LinuxFileHelper::MAJOR_MINOR($file)[1], 0, "Minor");
});

$t->run();

























