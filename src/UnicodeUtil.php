<?php

namespace HeyUpdate\Emoji;

class UnicodeUtil
{
    public static function convertUnicodeToString($cp)
    {
        $cp = hexdec($cp);

        if ($cp > 0x10000) {
            // 4 bytes
            return  chr(0xF0 | (($cp & 0x1C0000) >> 18)).
                chr(0x80 | (($cp & 0x3F000) >> 12)).
                chr(0x80 | (($cp & 0xFC0) >> 6)).
                chr(0x80 | ($cp & 0x3F));
        } elseif ($cp > 0x800) {
            // 3 bytes
            return  chr(0xE0 | (($cp & 0xF000) >> 12)).
                chr(0x80 | (($cp & 0xFC0) >> 6)).
                chr(0x80 | ($cp & 0x3F));
        } elseif ($cp > 0x80) {
            // 2 bytes
            return  chr(0xC0 | (($cp & 0x7C0) >> 6)).
                chr(0x80 | ($cp & 0x3F));
        } else {
            // 1 byte
            return chr($cp);
        }
    }

    public static function formatRegexString($string)
    {
        $out = '';

        for ($i = 0; $i < strlen($string); ++$i) {
            $char = ord(substr($string, $i, 1));
            if ($char >= 0x20 && $char < 0x80 && !in_array($char, [34, 39, 92], true)) {
                $out .= chr($char);
            } else {
                $out .= sprintf('\\x%02x', $char);
            }
        }

        return $out;
    }
}
