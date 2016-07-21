<?php

namespace phtar\utils;

/**
 * Description of ReadFileFunctions
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
interface ReadFileFunctions {

    /**
     * Read $length chars/bytes from the content
     * @param int $length
     * @return string
     */
    public function read($length);

    /**
     * Seek to a position ($offset) in the content
     * @param int $offset
     * @param int $whence the mode of seeking (SEEK_, SEEK_CUR, SEEK_END)
     * @return int
     */
    public function seek($offset, $whence = SEEK_SET);

    /**
     * Read one char from the content
     * @return char
     */
    public function getc();

    /**
     * Reads a line (\n) or a string up to the $length from the crontent
     * @param int $length
     * @return string
     */
    public function gets($length = null);

    /**
     * Returns the length of the content
     * @return int
     */
    public function length();

    /**
     * Checks if the end of the file is reached
     * @return boolean
     */
    public function eof();

    /**
     * Returns the type of access (how the stream can be accessed). See Table 1 of the fopen() reference.
     * @return string
     */
    public function getMode();
}
