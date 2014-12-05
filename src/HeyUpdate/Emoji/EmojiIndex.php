<?php

namespace HeyUpdate\Emoji;

class EmojiIndex
{
    /**
     * @var string
     */
    protected $configFile;

    /**
     * @var array
     */
    protected $emojis;

    /**
     * @var array
     */
    protected $emojiUnicodes = array();

    /**
     * @var array
     */
    protected $emojiNames = array();

    /**
     * @var string
     */
    protected $emojiUnicodeRegex;

    /**
     * @var string
     */
    protected $emojiNameRegex;

    public function __construct()
    {
        // TODO: Make the index file configurable
        $this->configFile = __DIR__ . '/../../../config/index.json';

        $this->loadConfig();
    }

    protected function loadConfig()
    {
        if (!is_file($this->configFile)) {
            throw new \InvalidArgumentException(sprintf('The emoji config file "%s" does not exist', $this->configFile));
        }

        $this->emojis = json_decode(file_get_contents($this->configFile), true);
        if ($this->emojis === false) {
            throw new \InvalidArgumentException(sprintf('Unable to parse the emoji config file "%s"', $this->configFile));
        }

        $emojiUnicodeRegexParts = array();
        foreach ($this->emojis as $index => $emoji) {
            if (isset($emoji['name'])) {
                // Create a map of emoji names to the hash index
                $this->emojiNames[$emoji['name']] = $index;
            }

            if (isset($emoji['aliases'])) {
                foreach ($emoji['aliases'] as $alias) {
                    // Create a map of emoji names to the hash index
                    $this->emojiNames[$alias] = $index;
                }
            }

            if (isset($emoji['unicode'])) {
                $string = '';
                foreach (explode('-', $emoji['unicode']) as $unicode) {
                    // Get string from unicode parts
                    $string .= $this->convertUnicodeToString($unicode);
                }

                // Create a map of unicode emoji characters to the hash index
                $this->emojiUnicodes[$string] = $index;
                $emojiUnicodeRegexParts[] = $this->formatRegexString($string);
            }
        }

        // Build the unicode regex
        $this->emojiUnicodeRegex = sprintf('/%s/', implode('|', $emojiUnicodeRegexParts));

        // Build the name regex
        $this->emojiNameRegex = sprintf('/:(%s):/', implode('|', array_map(function ($name) {
            return preg_quote($name, '/');
        }, array_keys($this->emojiNames))));
    }

    public function findByUnicode($unicode)
    {
        if (isset($this->emojiUnicodes[$unicode]) && isset($this->emojis[$this->emojiUnicodes[$unicode]])) {
            return $this->emojis[$this->emojiUnicodes[$unicode]];
        }
    }

    public function findByName($name)
    {
        if (isset($this->emojiNames[$name]) && isset($this->emojis[$this->emojiNames[$name]])) {
            return $this->emojis[$this->emojiNames[$name]];
        }
    }

    public function getEmojiUnicodeRegex()
    {
        return $this->emojiUnicodeRegex;
    }

    public function getEmojiNameRegex()
    {
        return $this->emojiNameRegex;
    }

    private function convertUnicodeToString($cp)
    {
        $cp = hexdec($cp);

        if ($cp > 0x10000) {
            // 4 bytes
            return  chr(0xF0 | (($cp & 0x1C0000) >> 18)) .
                chr(0x80 | (($cp & 0x3F000) >> 12)) .
                chr(0x80 | (($cp & 0xFC0) >> 6)) .
                chr(0x80 | ($cp & 0x3F));
        } else if ($cp > 0x800) {
            // 3 bytes
            return  chr(0xE0 | (($cp & 0xF000) >> 12)) .
                chr(0x80 | (($cp & 0xFC0) >> 6)) .
                chr(0x80 | ($cp & 0x3F));
        } else if ($cp > 0x80) {
            // 2 bytes
            return  chr(0xC0 | (($cp & 0x7C0) >> 6)) .
                chr(0x80 | ($cp & 0x3F));
        } else {
            // 1 byte
            return chr($cp);
        }
    }

    private function formatRegexString($string)
    {
        $out = '';

        for ($i = 0; $i < strlen($string); $i++) {
            $char = ord(substr($string, $i, 1));
            if ($char >= 0x20 && $char < 0x80 && !in_array($char, array(34, 39, 92))) {
                $out .= chr($char);
            } else {
                $out .= sprintf('\\x%02x', $char);
            }
        }

        return $out;
    }
}
