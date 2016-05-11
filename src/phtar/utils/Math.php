<?php

namespace phtar\utils;

/**
 * Description of Math
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
class Math {

    /**
     * Calculates the next integer where modolo $mod of the integer is 0. Starts at $int - 1. if $int modolo $mod is 0 it returns $int.
     * @param int $int
     * @param int $mod
     * @return int
     */
    public static function NEXT_OR_CURR_MOD_0($int, $mod) {
        $x = $int % $mod;
        if ($x == 0) {
            return $int;
        } elseif ($x < 0) {
            return $int + abs($x);
        } else {
            return $int + $mod - $x;
        }
    }

    /**
     * Returns the difference from $int to the next integer where the modolo $mod of the integer is 0.
     * @param int $int
     * @param int $mod
     * @return int
     */
    public static function DIFF_NEXT_MOD_0($int, $mod) {
        $x = $int % $mod;
        if ($x == 0) {
            return 0;
        } elseif ($x < 0) {
            return abs($x);
        } else {
            return $mod - $x;
        }
    }

}
