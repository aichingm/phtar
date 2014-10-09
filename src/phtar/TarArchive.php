<?php

/*
 * This file is part of: maLib - Mario Aichinger Library
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

namespace phtar;

/**
 * This class is designted to load all TarChunks form multible strings or 
 * files. It is also able to add new TarChunks to the archive. After adding all 
 * TarChunks use the __toString() method to get a string which contains a valid 
 * tar archive which you can for example save as myArchive.tar
 * @author Mario Aichinger aichingm@gmail.com
 * @copyright (c) 2014, Mario Aichinger
 */
class TarArchive implements \phtar\utils\interfaces\TarArchive {

    /**
     * Holds a list of all loaded TarChunks.
     * @var array
     */
    protected $array = array();

    /**
     * Loads TarChunks from a string. Throws a LengthException if strlen($string) % 512 == 0
     * @param string $string
     * @throws LengthException
     */
    public function load($string) {
        if (is_string($string) && strlen($string) % 512 == 0) {
            $this->readToArray($string);
        } else {
            $exception = new utils\LengthException(strlen($string) . " is no valid length for a tar archive");
            $exception->setLength(strlen($string));
            throw $exception;
        }
    }

    /**
     * Loads TarChunks from a file. Throws a Exception if thr file dont exists.
     * @param string $path
     * @throws Exception
     */
    public function loadFromFile($path) {
        if (is_file($path)) {
            $this->readToArray(file_get_contents($path));
        } else {
            throw new \Exception("File $path does not exist");
        }
    }

    /**
     * Explodes a string to TarChinks
     * @param string $string
     * @throws \ErrorException if the tar archive has no valid end of two empty 512 null-byte blocks
     */
    protected function readToArray($string) {
        for ($i = 0; $i < strlen($string);) {
            if ($this->hasNextNonEmptyBlock($string, $i)) {
                $chunk = new \phtar\TarChunk();
                $chunk->setMeta(substr($string, $i, 512));
                if (!utils\MetaBlockAnalyser::validateChecksum($chunk->getMeta())) {
                    throw new utils\NoValidTarException("The chunk with the name: " . utils\MetaBlockAnalyser::getName($chunk->getMeta()) . " does not pass the checksum validation test!");
                }
                $size = utils\MetaBlockAnalyser::getSize($chunk->getMeta());
                $i += 512;
                if ($size > 0) {
                    if ($size % 512 != 0) {
                        $size = 512 - $size % 512 + $size;
                    }
                    $chunk->setRawContent(substr($string, $i, $size));
                    $i += $size;
                }

                $this->array[] = $chunk;
            } else {
                if (!$this->isEmptyToEnd($string, $i + 512)) {
                    throw new \ErrorException("Unexpected end in tar archive. Missing second endblock (512 times ascii \0 char)");
                } else {
                    break;
                }
            }
        }
    }

    /**
     * Checks if the end of the archive is reached.
     * @param string $string
     * @param int $startIndex
     * @return boolean
     */
    protected function hasNextNonEmptyBlock($string, $startIndex) {
        for ($i = 0; $i < 512; $i++) {
            if ($string{$startIndex + $i} != "\x00") {
                return TRUE;
            }
        }
        return false;
    }

    /**
     * Checks if the rest of the archive is empty.
     * @param type $string
     * @param type $startIndex
     * @return boolean
     */
    protected function isEmptyToEnd($string, $startIndex) {
        for ($i = $startIndex; $i < strlen($string); $i++) {
            if ($string{$i} != "\x00") {
                return false;
            }
        }
        return true;
    }

    /**
     * returns all TarChunks which are loaded/added.
     * @return array
     */
    public function getChunks() {
        return $this->array;
    }

    /**
     * Converts all TarChunks to a valid .tar archive as a string.
     * @return string
     */
    public function __toString() {
        $string = "";
        $emptyBlockFactory = new \phtar\utils\EmptyBlockFactory();
        foreach ($this->array as $chunk) {
            $string .= $chunk->getMeta();
            $string .= $chunk->getRawContent();
        }
        return $string . $emptyBlockFactory->getString() . $emptyBlockFactory->getString();
    }

    /**
     * Adds a Chunk to the archive.
     * @param \phtar\utils\interfaces\TarChunk $chunk
     */
    public function addChunk(\phtar\utils\interfaces\TarChunk $chunk) {
        $this->array[] = $chunk;
    }

}
