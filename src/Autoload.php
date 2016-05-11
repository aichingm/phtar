<?php

/*
 * This file is part of: phtar
 * Copyright (C) 2016  Mario Aichinger
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

spl_autoload_register(function($classname) {
    $path = str_replace("\\", DIRECTORY_SEPARATOR, $classname);
    if (strpos($path, "phtar" . DIRECTORY_SEPARATOR) == 0 && is_file(__DIR__ . DIRECTORY_SEPARATOR . $path . ".php")) {
        require_once __DIR__ . DIRECTORY_SEPARATOR . $path . ".php";
    }
});
