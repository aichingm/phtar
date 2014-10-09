<?php

/*
 * This file is part of: phtar
 * Copyright (C) 2014  Mario Aichinger
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace phtar\utils;

/**
 * This class is designed to create arrays or strings with a length of 512
 * filling them with \0 characters.
 * @author Mario Aichinger aichingm@gmail.com
 * @copyright (c) 2014, Mario Aichinger
 */
class EmptyBlockFactory {

    /**
     * Returns a string with a length of 512 and filld with \0 characters.
     * @return string
     */
    public function getString() {
        return implode('', $this->getArray());
    }

    /**
     * Returns an array with 512 positions all set to \0 characters.
     * @return array
     */
    public function getArray() {
        return $this->createAsciiNullArray(512);
    }

    /**
     * Returns an array with $size positions all set to \0 characters.
     * @param int $size
     * @return array
     */
    private function createAsciiNullArray($size) {
        $array = array();
        if (!is_int($size)) {
            $size = 512;
        }
        for ($i = 0; $i < $size; $i++) {
            $array[$i] = "\0";
        }
        return $array;
    }

}
