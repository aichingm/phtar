<?php

namespace phtar\utils;

/**
 * Description of FileHandleHelper
 *
 * @author Mario Aichinger <aichingm@gmail.com>
 */
class FileHandleHelper {

    public static function CLONE_HANDLE($fd, $initSeek = -1) {
        if (!is_resource($fd)) {
            throw new \UnexpectedValueException("not a resource");
        }
        $meta = stream_get_meta_data($fd);
        if
        ($meta['wrapper_type'] !== 'plainfile' ||
                $meta['stream_type'] !== 'STDIO') {
            throw new \UnexpectedValueException("not a file descriptor");
        }
        if ($initSeek == -1) {
            $initSeek = ftell($fd);
        }
        $clone_fd = fopen($meta['uri'], $meta['mode']);
        fseek($clone_fd, $initSeek);
        return $clone_fd;
    }

    public static function COPY_H2H(ReadFileFunctions $from, WriteFileFunctions $to, $bufferSize = 8) {
        $from->seek(0);
        $bytesWriten = 0;
        while (!$from->eof()) {
            $bytesWriten += $to->write($from->read($bufferSize));
        }
        return $bytesWriten;
    }

}
