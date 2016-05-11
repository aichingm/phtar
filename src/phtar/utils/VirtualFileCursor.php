<?php

namespace phtar\utils;

/**
 * Description of VirtualFileCursor
 *
 * @author Mario Aichinger <aichingm@gmail.com>
 */
class VirtualFileCursor implements ReadFileFunctions {

    /**
     * Holds the handle of which this is a subset
     * @var ReadFileFunctions 
     */
    private $handle;

    /**
     * Holds the current opsition of the pointer
     * @var int 
     */
    private $offset = 0;

    /**
     * Holds the position at which the subset begins
     * @var int 
     */
    private $fileStart = 0;

    /**
     * Holds the position at which the subset ends
     * @var int 
     */
    private $fileEnd = 0;

    /**
     * Holds the state of the end-of-file-flag
     * @var boolean 
     */
    private $eofTried = false;

    const EOF_MODE_EOF = 0;
    const EOF_MODE_LENGTH = 1;
    const EOF_MODE_TRY_READ = 2;

    /**
     * Creates a new VirtualFileCursor object
     * @param \phtar\utils\ReadFileFunctions $handle 
     * @param int $fileOffset the position at which the subset starts
     * @param int $length the length of the subset
     */
    function __construct(ReadFileFunctions $handle, $fileOffset, $length) {
        $this->handle = $handle;
        $this->setBoundaries($fileOffset, $length);
    }

    /**
     * Read $length chars/bytes from the content
     * @param int $length
     * @return string
     */
    public function read($length) {
        $end = $this->offset + $length;
        if ($end > 0) {
            if ($end > $this->length()) {
                $length = $this->length() - $this->offset;
                $this->eofTried = true;
            }
            $string = $this->handle->read($length);
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
        if ($whence == SEEK_SET) {
            $newOffset = $this->fileStart + $offset;
            if ($newOffset >= $this->fileStart && $newOffset < $this->fileEnd) {
                $this->offset = $offset;
                return $this->handle->seek($this->fileStart + $offset, SEEK_SET);
            } else {
                return -1;
            }
        } else if ($whence == SEEK_CUR) {
            $newOffset = $this->fileStart + $this->offset + $offset;
            if ($newOffset >= $this->fileStart && $newOffset < $this->fileEnd) {
                $this->offset += $offset;
                return $this->handle->seek($offset, SEEK_CUR);
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
        return $this->fileEnd - $this->fileStart;
    }

    /**
     * Checks if the end of the file is reached
     * @return boolean
     */
    public function eof($mode = 0) {
        switch ($mode) {
            case self::EOF_MODE_LENGTH:
                return !($this->fileStart + $this->offset < $this->fileEnd);
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
        if ($this->fileStart + $this->offset < $this->fileEnd) {
            $this->offset++;
            return $this->handle->getc();
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
        $string = $this->handle->gets($length);
        if (strlen($string) + $this->offset > $this->length()) {
            $string = substr($string, 0, $this->length() - $this->offset);
            $this->offset = $this->length();
            $this->eofTried = true;
            if (strlen($string) === 0) {
                return false;
            }
        } else {
            $this->offset += strlen($string);
        }
        return $string;
    }

    /**
     * Sets the boundaries of the subset
     * @param int $offset the new start of the subset
     * @param int $length the new length of the subset
     */
    public function setBoundaries($offset, $length) {
        $this->fileStart = $offset;
        if ($this->fileStart < 1) {
            $this->fileStart = 0;
        }
        $this->fileEnd = $this->fileStart + $length;
        if ($this->fileEnd < 1) {
            $this->fileEnd = 0;
        }
        $this->seek(0);
    }

    /**
     * Clones the object. Needed to clone the handle.
     */
    public function __clone() {
        $this->handle = clone $this->handle;
    }

}
