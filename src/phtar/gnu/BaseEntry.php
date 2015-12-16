<?php

namespace phtar\gnu;

/**
 * Description of BaseEntry
 *
 * @author mario
 */
class BaseEntry extends PrimitiveEntry {

    public function __construct($name, $content) {
        $this->setName($name)->setContent($content);


        $this->setUserId(0)->setGroupId(0);
        //$this->setUserName("root")->setGroupName("root");
        $this->setLinkname("");
        $this->setMTime(time());
        $this->setMode(755);
        $size = strlen($this->getContent());
        $this->setSize($size)->setRealSize($size);
        $this->setType("0");
        $this->setDevMajor(0)->setDevMinor(0);
        
        $this->setATime(time());
        $this->setCTime(time());
        $this->setExtended(0);
        $this->setOffset(0);
        $this->setLongNames("")->setSparseList("");
        
        
        
        
        
    }

}
