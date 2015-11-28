<?php

namespace phtar\posixUs;

/**
 * Description of BaseEntry
 *
 * @author mario
 */
class DirEntry extends PrimitiveEntry {

    public function __construct($name) {
        $names = PrimitiveEntry::splitName($name);
        $this->setPrefix($names[0])->setName($names[1])->setContent("");


        $this->setUserId(0)->setGroupId(0);
        $this->setLinkname("");
        $this->setMTime(time());
        $this->setMode(755);
        $this->setSize(0);
        $this->setType("5");
        $this->setDevMajor(0)->setDevMinor(0);
        
        
        
    }

}
