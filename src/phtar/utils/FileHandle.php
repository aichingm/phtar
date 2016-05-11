<?php

namespace phtar\utils;

/**
 * Description of FileHandle
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
class FileHandle extends FileHandleReader implements FileFunctions {

    /**
     * Writes every thing in the buffer to the file
     */
    public function flush() {
        return fflush($this->handle);
    }

    /**
     * Writes data. If $length is set it will only write data this long.
     * @param string $string
     * @param int $length
     */
    public function write($string, $length = null) {
        if ($length) {
            return fwrite($this->handle, $string, $length);
        }
        return fwrite($this->handle, $string);
    }

}
