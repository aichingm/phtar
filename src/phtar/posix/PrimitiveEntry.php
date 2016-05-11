<?php

namespace phtar\posix;

/**
 * Description of PrimitiveEntry
 *
 * @author Mario Aichinger <aichingm@gmail.com>
 */
class PrimitiveEntry extends \phtar\v7\PrimitiveEntry implements Entry {

    /**
     * Holds the user name
     * @var string 
     */
    private $userName;

    /**
     * Holds the group name
     * @var string 
     */
    private $groupName;

    /**
     * Holds the Device Major Number
     * @var int 
     */
    private $devMajor;

    /**
     * Holds the Device Minor Number
     * @var int 
     */
    private $devMinor;

    /**
     * Holds the prefix
     * @var string 
     */
    private $prefix;

    /**
     * Creates a new PrimitiveEntry object
     */
    public function __construct() {
        parent::__construct();
        $this->setUserName("root")->setGroupName("wheel");
        $this->setDevMajor(0)->setDevMinor(0);
    }

    /**
     * Sets the name
     * @param string $name
     * @return \phtar\posix\PrimitiveEntry
     */
    public function setName($name) {
        $names = self::splitName($name);
        parent::setName($names[1]);
        $this->prefix = $names[0];
        return $this;
    }

    /**
     * Returns the user name
     * @return string
     */
    public function getUserName() {
        return $this->userName;
    }

    /**
     * Returns the user name
     * @return string
     */
    public function getGroupName() {
        return $this->groupName;
    }

    /**
     * Returns the Device Major Number
     * @return string 
     */
    public function getDevMajor() {
        return $this->devMajor;
    }

    /**
     * Returns the Device Minor Number
     * @return string 
     */
    public function getDevMinor() {
        return $this->devMinor;
    }

    /**
     * Returns the prefix
     * @return string 
     */
    public function getPrefix() {
        return $this->prefix;
    }

    /**
     * Sets the user name
     * @param string $userName
     * @return \phtar\posix\PrimitiveEntry
     */
    public function setUserName($userName) {
        $this->userName = $userName;
        return $this;
    }

    /**
     * Sets the group name
     * @param string $groupName
     * @return \phtar\posix\PrimitiveEntry
     */
    public function setGroupName($groupName) {
        $this->groupName = $groupName;
        return $this;
    }

    /**
     * Sets the Device Major Number
     * @param int $devMajor
     * @return \phtar\posix\PrimitiveEntry
     */
    public function setDevMajor($devMajor) {
        $this->devMajor = $devMajor;
        return $this;
    }

    /**
     * Sets the Device Minor Number
     * @param int $devMinor
     * @return \phtar\posix\PrimitiveEntry
     */
    public function setDevMinor($devMinor) {
        $this->devMinor = $devMinor;
        return $this;
    }

    /**
     * Splits the name in to the name and the prefix part array(0 => <prefix>, 1 => <name>)
     * @param string $name
     * @return array
     */
    public static function splitName($name) {
        $length = strlen($name);
        if ($length > 99) {
            $slash = strpos($name, "/", $length - 99);
            if ($slash > 154) {
                return array("", $name);
            } else {
                return array(substr($name, 0, $slash), substr($name, $slash));
            }
        } else {
            return array("", $name);
        }
    }

}
