<?php

namespace HeyUpdate\Emoji;

class EmojiIndexTest extends \PHPUnit_Framework_TestCase
{
    public $index;

    public function setUp()
    {
        $this->index = new EmojiIndex();
    }

    public function testFindByUnicodeReturnsEmojiHash()
    {
        $this->assertEquals(array(
            'unicode' => '1f623',
            'name' => 'persevere',
            'description' => 'persevering face',
            'aliases' => array()
        ), $this->index->findByUnicode('😣'));
    }

    public function testFindByUnicodeReturnsNullWhenNotFound()
    {
        $this->assertNull($this->index->findByUnicode('A'));
    }

    public function testFindByNameReturnsEmojiHash()
    {
        $this->assertEquals(array(
            'unicode' => '1f62b',
            'name' => 'tired_face',
            'description' => 'tired face',
            'aliases' => array()
        ), $this->index->findByName('tired_face'));
    }

    public function testFindByNameReturnsNullWhenNotFound()
    {
        $this->assertNull($this->index->findByName('testing'));
    }

    public function testGetEmojiUnicodeRegex()
    {
        $regex = $this->index->getEmojiUnicodeRegex();

        $this->assertEquals(4, preg_match_all($regex, '😣 🇺🇸 💀 👈'));
    }
}
