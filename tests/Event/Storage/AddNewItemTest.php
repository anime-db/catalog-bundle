<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CatalogBundle\Tests\Event\Storage;

use AnimeDb\Bundle\CatalogBundle\Event\Storage\AddNewItem;
use AnimeDb\Bundle\CatalogBundle\Entity\Item;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\FillerInterface;

/**
 * Test AddNewItem event.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class AddNewItemTest extends \PHPUnit_Framework_TestCase
{
    public function testEvent()
    {
        /* @var $item \PHPUnit_Framework_MockObject_MockObject|Item */
        $item = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Item');
        /* @var $filler1 \PHPUnit_Framework_MockObject_MockObject|FillerInterface */
        $filler1 = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\FillerInterface');
        /* @var $filler2 \PHPUnit_Framework_MockObject_MockObject|FillerInterface */
        $filler2 = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\FillerInterface');

        $event = new AddNewItem($item, $filler1);
        $this->assertEquals($item, $event->getItem());
        $this->assertInstanceOf('\Doctrine\Common\Collections\ArrayCollection', $event->getFillers());
        $this->assertEquals([$filler1], $event->getFillers()->toArray());

        $event->addFiller($filler2);
        $this->assertEquals([$filler1, $filler2], $event->getFillers()->toArray());
    }
}
