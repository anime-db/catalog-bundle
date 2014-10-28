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

use AnimeDb\Bundle\CatalogBundle\Entity\Widget\Type;

/**
 * Test type widget
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Entity\Widget
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class TypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test get/set item
     */
    public function testItem()
    {
        $type = new Type();
        $this->assertNull($type->getItem());

        $item = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Widget\Item');
        $item
            ->expects($this->once())
            ->method('setType')
            ->willReturnSelf()
            ->with($type);
        $this->assertEquals($type, $type->setItem($item));
        $this->assertEquals($type, $type->setItem($item));
        $this->assertEquals($item, $type->getItem());
    }
}
