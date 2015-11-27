<?php

namespace phtar\v7;

/**
 * Description of BaseEntry
 *
 * @author mario
 */
class BaseEntry extends PrimitiveEntry {

    public function __construct($name, $content) {
        $this->setName($name)->setContent($content);
        $this->setUserId(0)->setGroupId(0);
        $this->setLinkname("");
        $this->setMTime(time());
        $this->setMode(755);
        $this->setSize(strlen($this->getContent()));
        $this->setType("0");
    }

}
