<?php

namespace HeyUpdate\Emoji\Index;

interface IndexInterface
{
    /**
     * @param string $unicode
     *
     * @return array
     */
    public function findByUnicode($unicode);

    /**
     * @param string $name
     * @param array
     */
    public function findByName($name);

    /**
     * @return string
     */
    public function getEmojiUnicodeRegex();

    /**
     * @return string
     */
    public function getEmojiNameRegex();
}
