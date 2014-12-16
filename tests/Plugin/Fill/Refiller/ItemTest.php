<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Plugin\Fill\Refiller;

use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Item;

/**
 * Test refiller item
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Plugin\Fill\Refiller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test
     */
    public function test()
    {
        $item = new Item('my_name', ['my_data'], 'my_source', 'my_image', 'my_description');
        $this->assertEquals('my_name', $item->getName());
        $this->assertEquals(['my_data'], $item->getData());
        $this->assertEquals('my_source', $item->getSource());
        $this->assertEquals('my_image', $item->getImage());
        $this->assertEquals('my_description', $item->getDescription());
    }
}
