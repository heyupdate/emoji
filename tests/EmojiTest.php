<?php

namespace HeyUpdate\Emoji;

use HeyUpdate\Emoji\Index\CompiledIndex;

class EmojiTest extends \PHPUnit_Framework_TestCase
{
    public $emoji;

    public function setUp()
    {
        $this->emoji = new Emoji(new CompiledIndex());
    }

    public function testEmojiReplacesUnicodeEmojiWithImage()
    {
        $replacedString = $this->emoji->replaceEmojiWithImages('I ❤ Emoji');
        $this->assertSame('I <img alt="heart" class="emoji" src="https://twemoji.maxcdn.com/2/72x72/2764.png"> Emoji', $replacedString);
    }

    public function testEmojiReplacesNamedEmojiWithImage()
    {
        $replacedString = $this->emoji->replaceEmojiWithImages('Merry Christmas :santa:');
        $this->assertSame('Merry Christmas <img alt="santa" class="emoji" src="https://twemoji.maxcdn.com/2/72x72/1f385.png">', $replacedString);
    }

    public function testEmojiReplacesNamedEmojiWithImageWithCombinedEmoji()
    {
        $replacedString = $this->emoji->replaceEmojiWithImages(':woman-kiss-woman:');
        $this->assertSame('<img alt="woman-kiss-woman" class="emoji" src="https://twemoji.maxcdn.com/2/72x72/1f469-200d-2764-fe0f-200d-1f48b-200d-1f469.png">', $replacedString);
    }

    public function testEmojiReplacesNamedEmojiWithImageWithSkinToneEmoji()
    {
        $replacedString = $this->emoji->replaceEmojiWithImages(':+1::skin-tone-4:');
        $this->assertSame('<img alt="+1 skin-tone-4" class="emoji" src="https://twemoji.maxcdn.com/2/72x72/1f44d-1f3fd.png">', $replacedString);
    }

    public function testReplaceNamedWithUnicode()
    {
        $replacedString = $this->emoji->replaceNamedWithUnicode('I :heart: Emoji');

        $this->assertSame('I ❤ Emoji', $replacedString);
    }

    public function testReplaceUnicodeWithNamed()
    {
        $replacedString = $this->emoji->replaceUnicodeWithNamed('I ❤ Emoji');

        $this->assertSame('I :heart: Emoji', $replacedString);
    }

    public function testCountEmojiWithoutAnyEmoji()
    {
        $this->assertSame(0, $this->emoji->countEmoji('This does not contain any emoji'));
    }

    public function testCountEmojiWithEmoji()
    {
        $this->assertSame(3, $this->emoji->countEmoji('Three emoji for you! ❤❤ :smile:'));
    }
}
