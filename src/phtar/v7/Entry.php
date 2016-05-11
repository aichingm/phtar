<?php

namespace phtar\v7;

/**
 * Description of Entry
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
interface Entry {

    /**
     * Returns the name
     * @return string
     */
    public function getName();

    /**
     * Returns the mode
     * @return int
     */
    public function getMode();

    /**
     * Returns the user id
     * @return int
     */
    public function getUserId();

    /**
     * Returns the group id
     * @return int
     */
    public function getGroupId();

    /**
     * Returns the size
     * @return int
     */
    public function getSize();

    /**
     * Returns the last modification timestamp
     * @return int
     */
    public function getMTime();

    /**
     * Returns the type
     * @return mixed
     */
    public function getType();

    /**
     * Returns the linkname
     * @return string
     */
    public function getLinkname();

    /**
     * Copys this content to another \phtar\utils\WriteFileFunctions object. Returns the number of bytes copyed this way. 
     * @param \phtar\utils\WriteFileFunctions $destHandle 
     * @param int $bufferSize copys cunks of $bufferSize 
     * @return int 
     */
    public function copy2handle(\phtar\utils\WriteFileFunctions $destHandle, $bufferSize = 8);
}
