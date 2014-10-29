<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Entity;

use AnimeDb\Bundle\CatalogBundle\Entity\Item;

/**
 * Test item
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Item
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Entity\Item
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
     * Get url names
     *
     * @return array
     */
    public function getUrlNames()
    {
        return [
            ['foo', 'foo'],
            ['foo   bar', 'foo_bar'],
            ['foo bar: 1', 'foo_bar:_1'],
        ];
    }

    /**
     * Test get url name
     *
     * @dataProvider getUrlNames
     *
     * @param string $name
     * @param string $expected
     */
    public function testGetUrlName($name, $expected)
    {
        $this->item->setName($name);
        $this->assertEquals($expected, $this->item->getUrlName());
    }
}
