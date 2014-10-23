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

namespace phtar\utils\abstracts;

abstract class MetaBlock implements \phtar\utils\interfaces\MetaBlock {

    /**
     * If the file is a hardlink to an already added file it contians this 
     * file name else null.
     * @var string 
     */
    private $hardLinkTo;

    /**
     * Returns a \0 characters filled array with a length of $size.
     * @param int $size
     * @return array 
     */
    protected function createAsciiNullArray($size) {
        $array = array();
        for ($i = 0; $i < $size; $i++) {
            $array[$i] = "\0";
        }
        return $array;
    }

    /**
     * Returns a decimal interger of the sum of all characters in the $string. 
     * @param string $string
     * @return int a decimal interger of the sum of all characters in the $string parameter
     */
    protected function calculateSum($string = null) {
        $byte_array = unpack('C*', $string);
        $sum = 0;
        foreach ($byte_array as $char) {
            $sum += $char;
        }
        return $sum;
    }

    /**
     * Checks if the file is a hardlink to an already added file. If it is you 
     * can get the name of the file by calling 
     * \phtar\utils\interfaces\MetaBlock::getHardLinkTo();.
     * @param array $inodes
     * @param string $path
     * @return boolean
     */
    protected function isHardLink(array $inodes, $path) {
        foreach ($inodes as $inode => $linkPath) {
            if (fileinode($path) == $inode) {
                $this->hardLinkTo = $linkPath;
                return true;
            }
        }
        return false;
    }

    /**
     * Retirns the file size or 0 if the $path points to a directory.
     * @param type $path
     * @return int
     */
    protected function filesize($path) {
        if (is_dir($path)) {
            return 0;
        } else {
            return filesize($path);
        }
    }

    /**
     * Fills the $string parameter with leading $char's until its ($string) 
     * length is bigger/equal to $size.
     * @param string $string
     * @param int $size
     * @param string $char
     * @return string
     */
    protected function prependCharToSize($string, $size, $char) {
        $string .= "";
        while ($size > strlen($string)) {
            $string = $char . $string;
        }
        return $string;
    }

    /**
     * Writes a string char by char in the beginning of an array
     * @param type $string
     * @param array $array
     */
    protected function writeStringToArrayStart($string, array &$array) {
        $chars = str_split($string);
        //do not use count($chars) instead of strlen($string) an empty string will not work! #stupidPhp
        for ($i = 0; $i < strlen($string) && $i < count($array); $i++) {
            $array[$i] = $chars[$i];
        }
    }

    /**
     * Fills all positions of an array with the string in $string
     * @param string $string 
     * @param array $array
     */
    protected function fillArrayWith($string, array &$array) {
        for ($i = 0; $i < count($array); $i++) {
            $array[$i] = $string;
        }
    }

    /**
     * is an alias to the \implode('',$array); function
     * @param array $array
     * @return string
     */
    protected function implodeCharArray(array $array) {
        return implode('', $array);
    }

    /**
     * Returns the name of the file to which $path (from the constructor) 
     * is a hardlink to. NOTE: you hav to call isHardLink(..., ...); on 
     * $this before it returns something else as null.
     * @return string
     */
    public function getHardLinkTo() {
        return $this->hardLinkTo;
    }

}
