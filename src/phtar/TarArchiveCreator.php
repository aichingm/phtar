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

namespace phtar;

/**
 * This class is designed to help you with the creation of an archive in the 
 * tar format.
 * @author Mario Aichinger aichingm@gmail.com
 * @copyright (c) 2014, Mario Aichinger
 */
class TarArchiveCreator implements utils\interfaces\TarArchiveCreator {

    /**
     * Holds a TarChunkFactory which is used to create new chunks.
     * @var utils\interfaces\TarChunkFactory 
     */
    private $chunkFactory;

    /**
     * Holds a ContentBlockFactory which is used to create new content blocks
     * @var utils\interfaces\ContentBlockFactory 
     */
    private $contentBlockFactory;

    /**
     * Holds an array of all files wich sould be added to the archive
     * @var array 
     */
    private $files = array();

    /**
     * Holds an array of all chunks which should be added to the atchive.
     * @var array 
     */
    private $chunks = array();

    /**
     * creats a new TarArchiveCreator
     * @param \phtar\utils\interfaces\TarChunkFactory $chunkFactory
     * @param \phtar\utils\interfaces\ContentBlockFactory $contentBlockFactory
     * @param \phtar\utils\interfaces\TarArchive $tarArchive
     */
    public function __construct(
    utils\interfaces\TarChunkFactory &$chunkFactory, utils\interfaces\ContentBlockFactory &$contentBlockFactory) {
        $this->chunkFactory = $chunkFactory;
        $this->contentBlockFactory = $contentBlockFactory;
        $this->chunkFactory->setDefaultContentBlockFactory($this->contentBlockFactory);
    }

    /**
     * Adds a file or directory to the list. Returns true if the file is added 
     * to the list or false if not (it gets not added if the file or directory does not exists).
     * @param string $path
     * @return boolean
     */
    public function add($path) {
        $this->chunkFactory->checkPath($path);
        if (is_file($path) || is_dir($path)) {
            $this->files[] = $path;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Removes the given file from the list.
     * @param string $path
     */
    public function removePath($path) {
        $this->removeAllFromArray($path, $this->files);
    }

    /**
     * Returns all files and directories which have been added to the list.
     * @return array
     */
    public function getPaths() {
        return $this->files;
    }

    /**
     * Removes all values form the array which matches the path
     * @param string $path
     * @param array $array
     */
    private function removeAllFromArray($path, &$array) {
        foreach ($array as $key => $value) {
            if ($value == $path) {
                unset($array[$key]);
            }
        }
    }

    public function addChunk(utils\interfaces\TarChunk $chunk) {
        $this->chunks[] = $chunk;
    }

    /**
     * Creats the new Archive an returns it.
     * @return utils\interfaces\TarArchive
     */
    public function &create() {
        $tarArchive = new TarArchive();
        foreach ($this->files as $file) {
            foreach ($this->chunkFactory->create($file) as $chunk) {
                $tarArchive->addChunk($chunk);
            }
        }
        foreach ($this->chunks as $chunk) {
            $tarArchive->addChunk($chunk);
        }
        $this->chunkFactory->clear();
        return $tarArchive;
    }

    /**
     * Creates the TarArichive and returns a string which you can save to you 
     * .tar file.
     * @return string
     */
    public function __toString() {
        return $this->create() . "";
    }

    public function toString() {
        return $this->create() . "";
    }

}
