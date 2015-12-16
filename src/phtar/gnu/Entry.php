<?php

namespace phtar\gnu;

interface Entry extends \phtar\v7\Entry {

    public function getUserName();

    public function getGroupName();

    public function getDevMajor();

    public function getDevMinor();

    public function getATime();

    public function getCTime();

    public function getOffset();

    public function getLongnames();

    public function getSparseList();

    public function isExtended();

    public function getRealSize();
}
