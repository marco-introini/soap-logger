<?php

namespace Mintdev\Xml\Logger;

use Monolog\Handler\StreamHandler;
use Monolog\Level;

class RotateOnFileSizeHandler
{

    static public function make(string $filename, int $maxDimensions, int $maxFiles, Level $level): StreamHandler
    {
        if (file_exists($filename) && filesize($filename) > $maxDimensions) {
            $path_parts = pathinfo($filename);
            $pattern = $path_parts['dirname'].'/'.$path_parts['filename']."-%d.".$path_parts['extension'];

            // delete the last rotated file
            $fn = sprintf($pattern, $maxFiles);
            if (file_exists($fn)) {
                unlink($fn);
            }

            // shift file names (add '-%index' before the extension)
            for ($i = $maxFiles - 1; $i > 0; $i--) {
                $fn = sprintf($pattern, $i);
                if (file_exists($fn)) {
                    rename($fn, sprintf($pattern, $i + 1));
                }
            }
            rename($filename, sprintf($pattern, 1));
        }

        return new StreamHandler($filename, $level);
    }

}