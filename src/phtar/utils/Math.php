<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phtar\utils;

/**
 * Description of MAth
 *
 * @author mario
 */
class Math {

    public static function NEXT_OR_CURR_MOD_0($int, $mod) {
        $x = $int % $mod;
        if ($x == 0) {
            return $int;
        } else {
            return $int + $mod - $x;
        }
    }

    public static function DIFF_NEXT_MOD_0($int, $mod) {
        $x = $int % $mod;
        if ($x == 0) {
            return 0;
        } else {
            return $mod - $x;
        }
    }

}
