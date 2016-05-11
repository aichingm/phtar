<?php

/**
 * This file is part of Phtar
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
$t = new Pest\Pest(substr(basename(__FILE__), 0, -4));
$databox = new stdClass();
require_once __DIR__ . '/../src/Autoload.php';

use phtar\utils\Math;

$t->test("Test Math::DIFF_NEXT_MOD_0()", function() use($t, $databox) {

    $t->assertSame(Math::DIFF_NEXT_MOD_0(-511, 512), 511);
    $t->assertSame(Math::DIFF_NEXT_MOD_0(-513, 512), 1);

    $t->assertSame(Math::DIFF_NEXT_MOD_0(-1024, 512), 0);
    $t->assertSame(Math::DIFF_NEXT_MOD_0(-512, 512), 0);
    $t->assertSame(Math::DIFF_NEXT_MOD_0(0, 512), 0);
    $t->assertSame(Math::DIFF_NEXT_MOD_0(512, 512), 0);
    $t->assertSame(Math::DIFF_NEXT_MOD_0(1024, 512), 0);

    $t->assertSame(Math::DIFF_NEXT_MOD_0(1, 512), 511);
    $t->assertSame(Math::DIFF_NEXT_MOD_0(256, 512), 256);
    $t->assertSame(Math::DIFF_NEXT_MOD_0(1000, 512), 24);
});


$t->test("Test Math::DIFF_NEXT_MOD_0()", function() use($t, $databox) {


    $t->assertSame(Math::NEXT_OR_CURR_MOD_0(513, 512), 1024);
    $t->assertSame(Math::NEXT_OR_CURR_MOD_0(512, 512), 512);
    $t->assertSame(Math::NEXT_OR_CURR_MOD_0(511, 512), 512);
    $t->assertSame(Math::NEXT_OR_CURR_MOD_0(0, 512), 0);


    $t->assertSame(Math::NEXT_OR_CURR_MOD_0(-511, 512), 0);
    $t->assertSame(Math::NEXT_OR_CURR_MOD_0(-513, 512), -512);
});


$t->run();





























