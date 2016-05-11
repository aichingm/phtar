<?php

namespace phtar\posix;

/**
 * Description of Entry
 *
 * @author Mario Aichinger <aichingm@gmail.com>
 */
interface Entry extends \phtar\v7\Entry {

    /**
     * Returns the user name
     * @return string user name
     */
    public function getUserName();

    /**
     * Returns the group name
     * @return string group name
     */
    public function getGroupName();

    /**
     * Returns the Device Major Number
     * @return string Device Major Number
     */
    public function getDevMajor();

    /**
     * Returns the Device Minor Number
     * @return string Device Minor Number
     */
    public function getDevMinor();

    /**
     * Returns the prefix
     * @return string Device Minor Number
     */
    public function getPrefix();
}
