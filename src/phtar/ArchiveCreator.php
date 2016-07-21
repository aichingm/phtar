<?php

namespace phtar;

/**
 * Description of ArchiveCreator
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
interface ArchiveCreator extends \Countable {

    /**
     * Creates a new ArchiveCreator object
     * @param \phtar\utils\FileFunctions $handle the handy to which the archive should be writen
     * @throws \phtar\utils\PhtarException if the handle was not opened with the mode r+
     */
    public function __construct(\phtar\utils\FileFunctions $handle);

    /**
     * Counts the entries in the buffer
     * @overrides \Countable::count()
     * @param int $mode Default: COUNT_NORMAL
     * @return int
     */
    public function count($mode = COUNT_NORMAL);

    /**
     * Write all entries in the buffer to the handle and finalise the archive
     */
    public function write();
}
