<?php

namespace HeyUpdate\Emoji;

use HeyUpdate\Emoji\Index\IndexInterface;

class Emoji
{
    /**
     * @var IndexInterface
     */
    protected $index;

    /**
     * @var string
     */
    protected $imageHtmlTemplate;

    /**
     * @param IndexInterface $index
     * @param string         $imageHtmlTemplate
     */
    public function __construct(
        IndexInterface $index,
        $imageHtmlTemplate = '<img alt=":{{name}}:" class="emoji" src="https://twemoji.maxcdn.com/36x36/{{unicode}}.png">'
    ) {
        $this->setIndex($index);
        $this->setImageHtmlTemplate($imageHtmlTemplate);
    }

    /**
     * @return string
     */
    public function getImageHtmlTemplate()
    {
        return $this->imageHtmlTemplate;
    }

    /**
     * @param string $imageHtmlTemplate
     */
    public function setImageHtmlTemplate($imageHtmlTemplate)
    {
        $this->imageHtmlTemplate = $imageHtmlTemplate;
    }

    /**
     * @return IndexInterface
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param IndexInterface $index
     */
    public function setIndex(IndexInterface $index)
    {
        $this->index = $index;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function replaceEmojiWithImages($string)
    {
        $index = $this->getIndex();

        // NB: Named emoji should be replaced first as the string will then contain them in the image alt tags

        // Replace named emoji, e.g. ":smile:"
        $string = preg_replace_callback($index->getEmojiNameRegex(), function ($matches) use ($index) {
            $emoji = $index->findByName($matches[1]);

            return $this->renderTemplate($emoji);
        }, $string);

        // Replace unicode emoji
        $string = preg_replace_callback($index->getEmojiUnicodeRegex(), function ($matches) use ($index) {
            $emoji = $index->findByUnicode($matches[0]);

            return $this->renderTemplate($emoji);
        }, $string);

        return $string;
    }

    /**
     * @param array $emoji
     * @return string
     */
    private function renderTemplate(array $emoji)
    {
        return str_replace(
            [
                '{{name}}',
                '{{unicode}}',
                '{{description}}',
            ],
            [
                $emoji['name'],
                $emoji['unicode'],
                $emoji['description'],
            ],
            $this->imageHtmlTemplate
        );
    }

    /**
     * @param string string
     *
     * @return string
     */
    public function replaceNamedWithUnicode($string)
    {
        $index = $this->getIndex();

        return preg_replace_callback($index->getEmojiNameRegex(), function ($matches) use ($index) {
            $emoji = $index->findByName($matches[1]);

            return UnicodeUtil::convertUnicodeToString($emoji['unicode']);
        }, $string);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function replaceUnicodeWithNamed($string)
    {
        $index = $this->getIndex();

        return preg_replace_callback($index->getEmojiUnicodeRegex(), function ($matches) use ($index) {
            $emoji = $index->findByUnicode($matches[0]);

            return ':'.$emoji['name'].':';
        }, $string);
    }

    /**
     * @param string $string
     *
     * @return int
     */
    public function countEmoji($string)
    {
        $index = $this->getIndex();

        return preg_match_all($index->getEmojiNameRegex(), $string) + preg_match_all($index->getEmojiUnicodeRegex(), $string);
    }
}
