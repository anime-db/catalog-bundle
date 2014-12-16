<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Entity\Widget;

use AnimeDb\Bundle\CatalogBundle\Entity\Widget\Item;

/**
 * Test item widget
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Entity\Widget
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Item
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Entity\Widget\Item
     */
    protected $item;

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->item = new Item();
    }

    /**
     * Test get/set type
     */
    public function testType()
    {
        $this->assertNull($this->item->getType());

        $type = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Widget\Type');
        $type
            ->expects($this->once())
            ->method('setItem')
            ->willReturnSelf()
            ->with($this->item);
        $this->assertEquals($this->item, $this->item->setType($type));
        $this->assertEquals($this->item, $this->item->setType($type));
        $this->assertEquals($type, $this->item->getType());
    }

    /**
     * Test get/set genre
     */
    public function testGenre()
    {
        $this->assertEmpty($this->item->getGenres());

        $genre = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Widget\Genre');
        $genre
            ->expects($this->once())
            ->method('setItem')
            ->willReturnSelf()
            ->with($this->item);
        $this->assertEquals($this->item, $this->item->addGenre($genre));
        $this->assertEquals($this->item, $this->item->addGenre($genre));
        /* @var $coll \Doctrine\Common\Collections\Collection */
        $coll = $this->item->getGenres();
        $this->assertEquals(1, $coll->count());
        $this->assertEquals($genre, $coll->first());
    }
}
