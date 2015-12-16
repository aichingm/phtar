<?php

namespace phtar\gnu;

/**
 * Description of BaseEntry
 *
 * @author mario
 */
class DirEntry extends PrimitiveEntry {

    public function __construct($name) {
        $this->setName($name)->setContent("");
        $this->setUserId(0)->setGroupId(0);
        $this->setLinkname("");
        $this->setMTime(time());
        $this->setMode(755);
        $this->setSize(0);
        $this->setType("5");
        $this->setDevMajor(0)->setDevMinor(0);
    }

}
