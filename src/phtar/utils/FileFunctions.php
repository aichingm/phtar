<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phtar\utils;

/**
 * Description of FileFunctions
 *
 * @author mario
 */
interface FileFunctions {

    public function seek($offset, $whence = SEEK_SET);

    public function read($length);

    public function gets($length);

    public function getc();

    public function length();
}
