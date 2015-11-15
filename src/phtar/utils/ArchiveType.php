<?php

namespace phtar\utils;

class ArchiveType {

    const TYPE_V7 = 0;
    const TYPE_V7_GTAR = "this is so stupid";
    const TYPE_GNU = 1;
    const TYPE_POSIX_USTAR = 2; # IEEE Std 1003.1-1988 (``POSIX.1'')
    const TYPE_PAX = 3;

    public static function entryType(\phtar\utils\ReadFileFunctions $tarEntry) {
        $tarEntry->seek(257);
        $magicVersion = $tarEntry->read(8);
        if ($magicVersion === "ustar  " . "\0" . "  ") {
            return self::TYPE_GNU;
        } elseif ($magicVersion === "ustar" . "\0" . "00") {
            return self::TYPE_POSIX_USTAR;
        } else if (
                $magicVersion === "\0\0\0\0\0\0\0\0" &&
                $tarEntry->seek(257) === 0 &&
                self::onlyContains($tarEntry->read(255), "\0")
        ) {
            return self::TYPE_V7;
        }
    }

    private static function onlyContains($str, $char) {
        $size = strlen($str);
        for ($i = 0; $i < $size; $i++) {
            if ($str{$i} !== $char) {
                return false;
            }
        }
        return true;
    }

}
