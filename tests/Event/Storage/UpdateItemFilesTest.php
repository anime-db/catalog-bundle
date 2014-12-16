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

use AnimeDb\Bundle\CatalogBundle\Event\Storage\UpdateItemFiles;

/**
 * Test event UpdateItemFiles
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Event\Storage
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class UpdateItemFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test get item
     */
    public function testGetItem()
    {
        $item = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Item');
        $event = new UpdateItemFiles($item);
        $this->assertEquals($item, $event->getItem());
    }
}
