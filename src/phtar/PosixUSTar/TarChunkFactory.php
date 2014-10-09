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

namespace phtar\PosixUSTar;

use phtar\utils\TarException;

/**
 * This class creates TarChunks for adding them to an TarArchive
 * @author Mario Aichinger aichingm@gmail.com
 * @copyright (c) 2014, Mario Aichinger
 */
class TarChunkFactory implements \phtar\utils\interfaces\TarChunkFactory {

    /**
     * Holds all inodes an paths which are already added to the TarArchive
     * @var array 
     */
    private $inodes = array();

    /**
     * Holds a reference of a ContentBlockFactory which is used to create 
     * content blocks for TarChunks.
     * @var \phtar\utils\interfaces\ContentBlockFactory 
     */
    private $contentFactory = array();

    /**
     * Returns a array of TarChunks which represents the given path.
     * @param string $path
     * @return array a array of TarChunks
     */
    public function create($path) {
        $chunk = new \phtar\TarChunk();
        $block = new MetaBlock($path);
        $chunk->setMeta($block->create($this->inodes));
        if (is_file($path)) {
            if (\phtar\utils\MetaBlockAnalyser::getTypeflag($chunk->getMeta()) != 1) {
                $chunk->setRawContent($this->contentFactory->create($path));
            }
            $this->inodes[fileinode($path)] = $path;
        }
        return array($chunk);
    }

    /**
     * Sets the ContentBlockFactory which is used to create contentblcks for the TarChunk
     * @param \phtar\utils\interfaces\ContentBlockFactory $contentFactory
     */
    public function setDefaultContentBlockFactory(\phtar\utils\interfaces\ContentBlockFactory &$contentFactory) {
        $this->contentFactory = $contentFactory;
    }

    /**
     * Resets the Factory to its defaults.
     */
    public function clear() {
        $this->inodes = array();
    }

    public function checkPath($path) {
        if (strlen($path) > 99) {
            $index = strpos($path, '/', strlen($path) - 99);
            if (strlen($path) > 253 || (strlen($path) > 99 && (strpos($path, '/') === false || $index > 154 || strlen($path) - $index > 99))) {
                throw new TarException(TarException::MSG_PATH_TOO_LONG, TarException::CODE_PATH_TOO_LONG);
            }
        }
    }

}
