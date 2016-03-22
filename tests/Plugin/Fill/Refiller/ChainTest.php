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

use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Chain;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\RefillerInterface;
use AnimeDb\Bundle\CatalogBundle\Entity\Item;

/**
 * Test refiller plugin
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Plugin\Fill\Refiller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Get plugins
     *
     * @return array
     */
    public function getPlugins()
    {
        return [
            [false, false],
            [true, false],
            [false, true],
            [true, true]
        ];
    }

    /**
     * Test get plugins that can fill item
     *
     * @dataProvider getPlugins
     *
     * @param bool $is_can_refill
     * @param bool $is_can_search
     */
    public function testGetPluginsThatCanFillItem($is_can_refill, $is_can_search)
    {
        /* @var $item \PHPUnit_Framework_MockObject_MockObject|Item */
        $item = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Item');

        /* @var $plugin1 \PHPUnit_Framework_MockObject_MockObject|RefillerInterface */
        $plugin1 = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\RefillerInterface');
        $plugin1
            ->expects($this->once())
            ->method('isCanRefill')
            ->with($item, 'foo')
            ->will($this->returnValue($is_can_refill));
        $plugin1
            ->expects($is_can_refill ? $this->never() : $this->once())
            ->method('isCanSearch')
            ->with($item, 'foo')
            ->will($this->returnValue($is_can_search));
        $plugin1
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->will($this->returnValue('plugin1'));

        /* @var $plugin2 \PHPUnit_Framework_MockObject_MockObject|RefillerInterface */
        $plugin2 = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\RefillerInterface');
        $plugin2
            ->expects($this->once())
            ->method('isCanRefill')
            ->with($item, 'foo')
            ->will($this->returnValue(true));
        $plugin2
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->will($this->returnValue('plugin2'));

        $chain = new Chain();
        $chain->addPlugin($plugin1);
        $chain->addPlugin($plugin2);

        $actual = $chain->getPluginsThatCanFillItem($item, 'foo');
        if ($is_can_refill || $is_can_search) {
            $this->assertEquals([$plugin1, $plugin2], $actual);
        } else {
            $this->assertEquals([$plugin2], $actual);
        }
    }
}
