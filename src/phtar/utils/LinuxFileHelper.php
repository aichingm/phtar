<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phtar\utils;

/**
 * Description of LinuxFileHelper
 *
 * @author mario
 */
class LinuxFileHelper {

    public static function MAJOR_MINOR($filename) {
        $pathToExecutable = null;
        $return_var = -1;
        //find the ls executable
        exec("sh -c \"which ls\"", $pathToExecutable, $return_var);
        if ($return_var === 0) {
            $line = exec("{$pathToExecutable[0]} -l " . escapeshellarg($filename));
            if ($line{0} == 'c' || $line{0} == 'b') {
                $parts = explode(" ", $line);
                return array(intval(trim($parts[4])), intval(trim($parts[5])));
            }
            return array(0, 0);
        }
        throw new \Exception("Unable to execute the ls command. Are you on unix?");
    }

}
