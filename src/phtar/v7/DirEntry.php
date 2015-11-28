<?php

namespace phtar\v7;

/**
 * Description of BaseEntry
 *
 * @author mario
 */
class DirEntry extends PrimitiveEntry {

    public function __construct($name) {
        if($name{strlen($name)-1} != "/"){
            $name .= "/";
        }
        $this->setName($name)->setContent("");
        $this->setUserId(0)->setGroupId(0);
        $this->setLinkname("");
        $this->setMTime(time());
        $this->setMode(755);
        $this->setSize(0);
        $this->setType("5");
    }

}
