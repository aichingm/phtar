<?php

namespace phtar\gnu;

/**
 * Description of Entry
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
interface Entry extends \phtar\v7\Entry {

    /**
     * Returns the name of the owner
     * @return string
     */
    public function getUserName();

    /**
     * Returns the name of the owning group
     * @return string
     */
    public function getGroupName();

    /**
     * Returns the Device Major Number
     * @return int
     */
    public function getDevMajor();

    /**
     * Returns the Device Minor Number
     * @return int
     */
    public function getDevMinor();

    /**
     * Returns the last time the file was accessed as timestamp.
     * @return int
     */
    public function getATime();

    /**
     * Returns the last time the file or the inode was changed as timestamp.
     * @return int
     */
    public function getCTime();

    /**
     * Returns the offset where this file fragment begins.
     * @return int 
     */
    public function getOffset();

    /**
     * Unused!
     * @return string a string with a fixed size of 4
     */
    public function getLongnames();

    /**
     * Returns a list of sparse fragments
     * @todo Read posible additional headers 
     * @return array
     */
    public function getSparseList();

    /**
     * Returns true if the next 512 bytes are used as a sparse extension header
     * @return bool
     */
    public function isExtended();

    /**
     * Returns the file's complete size. (Check the 'M'-type)
     * @return int 
     */
    public function getRealSize();
}
