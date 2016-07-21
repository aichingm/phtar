<?php

namespace phtar\utils;

/**
 * Description of WriteFileFunctions
 *
 * @author Mario Aichinger <aichingm@gmail.com>
 */
interface WriteFileFunctions {

    /**
     * Writes every thing in the buffer to the file
     */
    public function flush();

    /**
     * Writes data. If $length is set it will only write data this long.
     * @param string $string
     * @param int $length
     */
    public function write($string, $length = null);

    /**
     * Seek to a position ($offset) in the content
     * @param int $offset
     * @param int $whence the mode of seeking (SEEK_, SEEK_CUR, SEEK_END)
     * @return int
     */
    public function seek($offset, $whence = SEEK_SET);

    /**
     * Returns the type of access (how the stream can be accessed). See Table 1 of the fopen() reference.
     * @return string
     */
    public function getMode();
}
