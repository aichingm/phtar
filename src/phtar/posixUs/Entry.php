<?php

namespace phtar\posixUs;

interface Entry extends \phtar\v7\Entry {

    public function getRealName();

    public function getUserName();

    public function getGroupName();

    public function getDevMajor();

    public function getDevMinor();

    public function getPrefix();
}
