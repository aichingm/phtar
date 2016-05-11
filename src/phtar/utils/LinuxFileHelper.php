<?php

namespace phtar\utils;

/**
 * Description of LinuxFileHelper
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
class LinuxFileHelper {

    /**
     * Returns array with the size of two. [0 => <major>, 1 => <minor>]. This function will only work on UNIX systems
     * @param string $filename the filename of which devmajor and dev minor should be evaluated
     * @return array An array with the size of two. [0 => <major>, 1 => <minor>]
     * @throws \Exception 
     */
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
        throw new \Exception("Unable to execute the >ls< command. Are you on unix?");
    }

}
