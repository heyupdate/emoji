<?php

namespace HeyUpdate\Emoji;

use HeyUpdate\Emoji\Index\CompiledIndex;

class IndexTest extends \PHPUnit_Framework_TestCase
{
    public $index;

    public function setUp()
    {
        $this->index = new CompiledIndex();
    }

    public function testFindByUnicodeReturnsEmojiHash()
    {
        $this->assertSame([
            'unicode' => '1f623',
            'name' => 'persevere',
            'description' => 'persevering face',
            'aliases' => [],
        ], $this->index->findByUnicode('😣'));
    }

    public function testFindByUnicodeReturnsNullWhenNotFound()
    {
        $this->assertNull($this->index->findByUnicode('A'));
    }

    public function testFindByNameReturnsEmojiHash()
    {
        $this->assertSame([
            'unicode' => '1f62b',
            'name' => 'tired_face',
            'description' => 'tired face',
            'aliases' => [],
        ], $this->index->findByName('tired_face'));
    }

    public function testFindByNameReturnsNullWhenNotFound()
    {
        $this->assertNull($this->index->findByName('testing'));
    }

    public function testGetEmojiUnicodeRegex()
    {
        $regex = $this->index->getEmojiUnicodeRegex();

        $this->assertSame(4, preg_match_all($regex, '😣 🇺🇸 💀 👈'));
    }
}
