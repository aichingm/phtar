<?php

namespace phtar\posixUs;

/**
 * Description of BaseEntry
 *
 * @author mario
 */
class BaseEntry extends PrimitiveEntry {

    public function __construct($name, $content) {
        $this->setRealName($name);

        $names = PrimitiveEntry::splitName($name);
        $this->setPrefix($names[0])->setName($names[1])->setContent($content);


        $this->setUserId(0)->setGroupId(0);
        //$this->setUserName("root")->setGroupName("root");
        $this->setLinkname("");
        $this->setMTime(time());
        $this->setMode(755);
        $this->setSize(strlen($this->getContent()));
        $this->setType("0");
        $this->setDevMajor(0)->setDevMinor(0);
    }

}
