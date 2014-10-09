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
 * This class is designed to load files and make there length a multibe of 512 
 * by appending \0 characters to it.
 * @author Mario Aichinger aichingm@gmail.com
 * @copyright (c) 2014, Mario Aichinger
 */
class ContentFixed512Factory implements interfaces\ContentBlockFactory {
    /**
     * Load file and makes its length a multibe of 512 by appending 
     * \0 characters to it.
     * @param string $file
     * @return string
     */
    public function create($file) {
        $content = file_get_contents($file);
        while (strlen($content) % 512 != 0) {
            $content .= "\0";
        }
        return $content;
    }

}