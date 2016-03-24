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

use AnimeDb\Bundle\CatalogBundle\Entity\Widget\Genre;
use AnimeDb\Bundle\CatalogBundle\Entity\Widget\Item;

/**
 * Test genre widget
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Entity\Widget
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class GenreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test get/set item
     */
    public function testItem()
    {
        $genre = new Genre();
        $this->assertNull($genre->getItem());

        /* @var $item \PHPUnit_Framework_MockObject_MockObject|Item */
        $item = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Widget\Item');
        $item
            ->expects($this->once())
            ->method('addGenre')
            ->with($genre)
            ->will($this->returnSelf());
        $this->assertEquals($genre, $genre->setItem($item));
        $this->assertEquals($genre, $genre->setItem($item));
        $this->assertEquals($item, $genre->getItem());
    }
}
