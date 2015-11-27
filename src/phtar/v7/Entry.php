<?php

namespace phtar\v7;

interface Entry {

    public function getName();

    public function getMode();

    public function getUserId();

    public function getGroupId();

    public function getSize();

    public function getMTime();

    public function getType();

    public function getLinkname();

    public function copy2handle(\phtar\utils\WriteFileFunctions $destHandle, $bufferSize = 8);
}
