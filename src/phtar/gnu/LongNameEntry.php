<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phtar\gnu;

/**
 * Description of LongLinkEntry
 *
 * @author mario
 */
class LongNameEntry extends PrimitiveEntry {

    public function __construct($name) {

        $this->setName("././@LongLink")->setContent($name);
        $this->setUserId(0)->setGroupId(0);
        $this->setUserName("root")->setGroupName("root");
        $this->setMode(755);
        $size = strlen($this->getContent());
        $this->setSize($size)->setRealSize($size);
        $this->setType(Archive::ENTRY_TYPE_LONG_PATHNAME);
        $this->setDevMajor(0)->setDevMinor(0);

        $this->setMTime(time());
        $this->setATime(0);
        $this->setCTime(0);

        $this->setExtended(0);
        $this->setOffset(0);
        $this->setLongNames("")->setSparseList("");
    }

}
