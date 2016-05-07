<?php

namespace phtar\posix;

/**
 * Description of HardlinkEntry
 *
 * @author mario
 */
class HardlinkEntry extends \phtar\gnu\PrimitiveEntry implements Entry {

    public function __construct($name, Entry $linkTo) {
        parent::__construct();
        $this->setName($name)->setLinkname($linkTo->getName());
    }

}
