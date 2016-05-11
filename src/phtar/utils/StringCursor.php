<?php

namespace phtar\utils;

/**
 * Description of StringCursor
 *
 * @author Mario Aichinger <aichingm@gmail.com>
 */
class StringCursor implements ReadFileFunctions {

    /**
     * Holds the data
     * @var string 
     */
    private $str;

    /**
     * Holds the length of the data
     * @var int  
     */
    private $size;

    /**
     * Holds the current position of the pointer
     * @var int 
     */
    private $offset = 0;

    /**
     * Holds the state of the end-of-file-flag
     * @var boolean 
     */
    private $eofTried = false;

    const EOF_MODE_EOF = 0;
    const EOF_MODE_LENGTH = 1;
    const EOF_MODE_TRY_READ = 2;

    /**
     * Creates a new StringCursor object
     * @param string $string the data to operate on
     */
    function __construct($string) {
        $this->setString($string);
    }

    /**
     * Read $length chars/bytes from the content
     * @param int $length
     * @return string
     */
    public function read($length) {
        $end = $this->offset + $length;
        if ($end > 0) {
            if ($end > $this->size) {
                $length = $this->size - $this->offset;
                $this->eofTried = true;
            }
            $string = substr($this->str, $this->offset, $length);
            $this->offset += $length;
            return $string;
        }
        return false;
    }

    /**
     * Seek to a position ($offset) in the content
     * @param int $offset
     * @param int $whence the mode of seeking (SEEK_, SEEK_CUR, SEEK_END)
     * @return int
     */
    public function seek($offset, $whence = SEEK_SET) {
        $this->eofTried = false;
        if ($whence == SEEK_SET) {
            if ($offset >= 0 && $offset < $this->size) {
                $this->offset = $offset;
                return 0;
            } else {
                return -1;
            }
        } else if ($whence == SEEK_CUR) {
            $newOffset = $this->offset + $offset;
            if ($newOffset >= 0 && $newOffset < $this->size) {
                $this->offset += $offset;
                return 0;
            } else {
                return -1;
            }
        }
        return -1;
    }

    /**
     * Returns the length of the content
     * @return int
     */
    public function length() {
        return $this->size;
    }

    /**
     * Checks if the end of the file is reached
     * @return boolean
     */
    public function eof($mode = 0) {
        switch ($mode) {
            case self::EOF_MODE_LENGTH:
                return !($this->offset < $this->size);
            case self::EOF_MODE_TRY_READ:
                if ($this->getc() === false) {
                    return true;
                } else {
                    $this->seek(-1, SEEK_CUR);
                    return false;
                }
            case 0:
            default :
                return $this->eofTried;
        }
    }

    /**
     * Read one char from the content
     * @return char
     */
    public function getc() {
        if (isset($this->str{$this->offset})) {
            return $this->str{$this->offset++};
        } else {
            $this->eofTried = true;
            return false;
        }
    }

    /**
     * Reads a line (\n) or a string up to the $length from the crontent
     * @param int $length
     * @return string
     */
    public function gets($length = null) {
        $nlPos = strpos($this->str, "\n", $this->offset) + 1;
        if ($nlPos === false) {
            $nlPos = $this->size - $this->offset;
        }
        if ($length == null || $nlPos < $length) {
            $length = $nlPos - $this->offset;
        }
        return $this->read($length);
    }

    /**
     * Sets the date to operate on
     * @param string $string
     */
    public function setString($string) {
        $this->str = $string;
        $this->size = strlen($string);
        $this->offset = 0;
        $this->eofTried = false;
    }

}
