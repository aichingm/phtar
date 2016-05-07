<?php

namespace phtar\posix;

interface Entry extends \phtar\v7\Entry {

    public function getUserName();

    public function getGroupName();

    public function getDevMajor();

    public function getDevMinor();
}
