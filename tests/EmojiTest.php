<?php

namespace HeyUpdate\Emoji;

class EmojiTest extends \PHPUnit_Framework_TestCase
{
    public $emoji;

    public function setUp()
    {
        $index = new EmojiIndex();
        $this->emoji = new Emoji($index, 'http://twemoji.maxcdn.com/36x36/%s.png');
    }

    public function testEmojiReplacesUnicodeEmojiWithImage()
    {
        $replacedString = $this->emoji->replaceEmojiWithImages('I ❤ Emoji');
        $this->assertSame('I <img alt=":heart:" class="emoji" src="http://twemoji.maxcdn.com/36x36/2764.png"> Emoji', $replacedString);
    }

    public function testEmojiReplacesNamedEmojiWithImage()
    {
        $replacedString = $this->emoji->replaceEmojiWithImages('Merry Christmas :santa:');
        $this->assertSame('Merry Christmas <img alt=":santa:" class="emoji" src="http://twemoji.maxcdn.com/36x36/1f385.png">', $replacedString);
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
        $this->assertEquals(3, $this->emoji->countEmoji('Three emoji for you! ❤❤ :smile:'));
    }

    public function testEmojiReplacesNamedEmojiWithMacros()
    {
        $replacedString = $this->emoji->replaceEmojiWithMacros('❤');
        $this->assertEquals(':heart:', $replacedString);
    }

}
