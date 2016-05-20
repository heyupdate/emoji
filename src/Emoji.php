<?php

namespace HeyUpdate\Emoji;

class Emoji
{
    /**
     * @var EmojiIndex
     */
    protected $index;

    /**
     * The sprintf format used to generate the asset URLs, e.g. "http://emojis.com/16x16/%s.png"
     *
     * @var string
     */
    protected $assetUrlFormat;
    protected $htmlAttrs;

    public function __construct(EmojiIndex $index, $assetUrlFormat, $htmlAttrs = '')
    {
        $this->setIndex($index);
        $this->setAssetUrlFormat($assetUrlFormat);
        $this->htmlAttrs = $htmlAttrs;
    }

    public function getAssetUrlFormat()
    {
        return $this->assetUrlFormat;
    }

    public function setAssetUrlFormat($assetUrlFormat)
    {
        $this->assetUrlFormat = $assetUrlFormat;
    }

    public function getIndex()
    {
        return $this->index;
    }

    public function setIndex(EmojiIndex $index)
    {
        $this->index = $index;
    }

    public function replaceEmojiWithImages($string)
    {
        $htmlFormat = '<img alt=":%s:" class="emoji" src="' . $this->assetUrlFormat . '"'.$this->htmlAttrs.'>';
        return $this->replaceEmojiWithPattern($string, $htmlFormat);
    }

    protected function replaceEmojiWithPattern($string, $pattern){
        $index = $this->getIndex();
        // NB: Named emoji should be replaced first as the string will then contain them in the image alt tags

        // Replace named emoji, e.g. ":smile:"
        $string = preg_replace_callback($index->getEmojiNameRegex(), function ($matches) use ($index, $pattern) {
            $emoji = $index->findByName($matches[1]);

            return sprintf($pattern, $emoji['name'], $emoji['unicode']);
        }, $string);

        // Replace unicode emoji
        $string = preg_replace_callback($index->getEmojiUnicodeRegex(), function ($matches) use ($index, $htmlFormat) {
            $emoji = $index->findByUnicode($matches[0]);

            return sprintf($htmlFormat, $emoji['name'], $emoji['unicode']);
        }, $string);

        return $string;

    }
    public function replaceEmojiWithMacros($string)
    {
        $htmlFormat = ':%s:';
        return $this->replaceEmojiWithPattern($string, $htmlFormat);
    }

    public function countEmoji($string)
    {
        $index = $this->getIndex();

        return preg_match_all($index->getEmojiNameRegex(), $string) + preg_match_all($index->getEmojiUnicodeRegex(), $string);
    }
}
