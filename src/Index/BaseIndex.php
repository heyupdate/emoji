<?php

namespace HeyUpdate\Emoji\Index;

class BaseIndex implements IndexInterface
{
    /**
     * @var array
     */
    protected $emojis = [];

    /**
     * @var array
     */
    protected $emojiUnicodes = [];

    /**
     * @var array
     */
    protected $emojiNames = [];

    /**
     * @var string
     */
    protected $emojiUnicodeRegex = '';

    /**
     * @var string
     */
    protected $emojiNameRegex = '';

    /**
     * @param string $unicode
     *
     * @return array
     */
    public function findByUnicode($unicode)
    {
        if (isset($this->emojiUnicodes[$unicode]) && isset($this->emojis[$this->emojiUnicodes[$unicode]])) {
            return $this->emojis[$this->emojiUnicodes[$unicode]];
        }
    }

    /**
     * @param string $name
     * @param array
     */
    public function findByName($name)
    {
        if (isset($this->emojiNames[$name]) && isset($this->emojis[$this->emojiNames[$name]])) {
            return $this->emojis[$this->emojiNames[$name]];
        }
    }

    /**
     * @return string
     */
    public function getEmojiUnicodeRegex()
    {
        return $this->emojiUnicodeRegex;
    }

    /**
     * @return string
     */
    public function getEmojiNameRegex()
    {
        return $this->emojiNameRegex;
    }

    /**
     * @return array
     */
    public function getEmojis()
    {
        return $this->emojis;
    }
}
