<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Event\Storage;

use AnimeDb\Bundle\CatalogBundle\Event\Storage\UpdateItemFiles;
use AnimeDb\Bundle\CatalogBundle\Entity\Item;

/**
 * Test event UpdateItemFiles.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class UpdateItemFilesTest extends \PHPUnit_Framework_TestCase
{
    public function testGetItem()
    {
        /* @var $item \PHPUnit_Framework_MockObject_MockObject|Item */
        $item = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Item');
        $event = new UpdateItemFiles($item);
        $this->assertEquals($item, $event->getItem());
    }
}
