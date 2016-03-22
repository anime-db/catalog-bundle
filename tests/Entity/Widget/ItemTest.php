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
use AnimeDb\Bundle\CatalogBundle\Entity\Widget\Type;
use AnimeDb\Bundle\CatalogBundle\Entity\Widget\Genre;

/**
 * Test item widget
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Entity\Widget
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \AnimeDb\Bundle\CatalogBundle\Entity\Widget\Item
     */
    protected $item;

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

        /* @var $type \PHPUnit_Framework_MockObject_MockObject|Type */
        $type = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Widget\Type');
        $type
            ->expects($this->once())
            ->method('setItem')
            ->with($this->item)
            ->will($this->returnSelf());
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

        /* @var $genre \PHPUnit_Framework_MockObject_MockObject|Genre */
        $genre = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Widget\Genre');
        $genre
            ->expects($this->once())
            ->method('setItem')
            ->with($this->item)
            ->will($this->returnSelf());
        $this->assertEquals($this->item, $this->item->addGenre($genre));
        $this->assertEquals($this->item, $this->item->addGenre($genre));
        /* @var $coll \Doctrine\Common\Collections\Collection */
        $coll = $this->item->getGenres();
        $this->assertEquals(1, $coll->count());
        $this->assertEquals($genre, $coll->first());
    }
}
