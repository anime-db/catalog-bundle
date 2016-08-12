<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CatalogBundle\Tests\Plugin\Fill\Search;

use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Item;

/**
 * Test search item.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ItemTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $item = new Item('my_name', 'my_link', 'my_image', 'my_description', 'my_source');
        $this->assertEquals('my_name', $item->getName());
        $this->assertEquals('my_link', $item->getLink());
        $this->assertEquals('my_image', $item->getImage());
        $this->assertEquals('my_description', $item->getDescription());
        $this->assertEquals('my_source', $item->getSource());
    }
}
