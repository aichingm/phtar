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

namespace phtar\GnuUSTar;

/**
 * This class creates TarChunks for adding them to an TarArchive
 * @author Mario Aichinger aichingm@gmail.com
 * @copyright (c) 2014, Mario Aichinger
 */
class TarChunkFactory implements \phtar\utils\interfaces\TarChunkFactory {

    /**
     * Holds all inodes an paths which are already added to the TarArchive
     * array("8722344", "the/path/to")
     * @var array 
     */
    private $inodes = array();

    /**
     * Holds a reference of a ContentBlockFactory which is used to create 
     * content blocks for TarChunks.
     * @var \phtar\utils\interfaces\ContentBlockFactory 
     */
    private $contentFactory = null;

    /**
     * Returns a array of TarChunks which represents the given path.
     * @param string $path
     * @return array a array of TarChunks
     */
    public function create($path) {
        $chunks = array();
        $addContent = true;
        $lastChunk = new \phtar\TarChunk();
        try {
            $block = new MetaBlock($path);
            $lastChunk->setMeta($block->create($this->inodes));
        } catch (HasMoreChunksException $e) {
            foreach ($e->getChunks() as $meta => $content) {
                $chunk = new \phtar\TarChunk();
                $chunk->setMeta($meta);
                $chunk->setRawContent($content);
                $chunks[] = $chunk;
            }
            $lastChunk->setMeta($e->getLastMeta());
            $content = $e->getLastContent();
            if ($content != null) {
                $addContent = false;
            }
        }
        if ($addContent) {
            if (is_file($path)) {
                if (!in_Array(\phtar\utils\MetaBlockAnalyser::getTypeflag($lastChunk->getMeta()), array("L", 1))) {
                    $lastChunk->setRawContent($this->contentFactory->create($path));
                }
            }
        }
        $chunks[] = $lastChunk;
        if (is_file($path)) {
            $this->inodes[fileinode($path)] = $path;
        }
        return $chunks;
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
        
    }

}
