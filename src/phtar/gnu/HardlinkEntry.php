<?php

namespace phtar\gnu;

/**
 * Description of HardlinkEntry
 *
 * @author Mario Aichinger
 */
class HardlinkEntry extends \phtar\gnu\PrimitiveEntry {

    public function __construct($name, phtar\gnu\Entry $linkTo) {
        parent::__construct();
        $this->setName($name)->setLinkname($linkTo->getName());
    }

}
