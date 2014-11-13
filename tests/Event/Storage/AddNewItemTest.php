<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Event\Storage;

use AnimeDb\Bundle\CatalogBundle\Event\Storage\AddNewItem;

/**
 * Test AddNewItem event
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Event\Storage
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class AddNewItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test event
     */
    public function testEvent()
    {
        $item = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Item');
        $filler = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Filler');
        $event = new AddNewItem($item, $filler);
        $this->assertEquals($item, $event->getItem());
        $this->assertInstanceOf('\Doctrine\Common\Collections\ArrayCollection', $event->getFillers());
        $this->assertEquals([$filler], $event->getFillers()->toArray());
    }
}