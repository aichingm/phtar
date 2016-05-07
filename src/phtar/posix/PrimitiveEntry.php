<?php

namespace phtar\posix;

/**
 * Description of LinuxFileEntry
 *
 * @author mario
 */
class PrimitiveEntry extends \phtar\v7\PrimitiveEntry implements Entry {

    private $userName, $groupName, $devMajor, $devMinor, $prefix;

    public function __construct() {
        parent::__construct();
        $this->setUserName("root")->setGroupName("wheel");
        $this->setDevMajor(0)->setDevMinor(0);
    }

    public function setName($name) {
        $names = self::splitName($name);
        parent::setName($names[1]);
        $this->prefix = $names[0];
        return $this;
    }

    public function getUserName() {
        return $this->userName;
    }

    public function getGroupName() {
        return $this->groupName;
    }

    public function getDevMajor() {
        return $this->devMajor;
    }

    public function getDevMinor() {
        return $this->devMinor;
    }

    public function getPrefix() {
        return $this->prefix;
    }

    public function setUserName($userName) {
        $this->userName = $userName;
        return $this;
    }

    public function setGroupName($groupName) {
        $this->groupName = $groupName;
        return $this;
    }

    public function setDevMajor($devMajor) {
        $this->devMajor = $devMajor;
        return $this;
    }

    public function setDevMinor($devMinor) {
        $this->devMinor = $devMinor;
        return $this;
    }

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
