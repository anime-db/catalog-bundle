<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Plugin;

use AnimeDb\Bundle\CatalogBundle\Plugin\Chain;
use AnimeDb\Bundle\CatalogBundle\Plugin\Plugin;

/**
 * Test plugin chain
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Plugin
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Chain
     */
    protected $chain;

    protected function setUp()
    {
        $this->chain = $this->getMockForAbstractClass('\AnimeDb\Bundle\CatalogBundle\Plugin\Chain');
    }

    /**
     * Test get plugin
     */
    public function testGetPlugin()
    {
        $this->assertFalse($this->chain->hasPlugins());

        /* @var $plugin_first \PHPUnit_Framework_MockObject_MockObject|Plugin */
        $plugin_first = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Plugin\Plugin');
        $plugin_first
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->will($this->returnValue('foo first'));
        $plugin_first
            ->expects($this->once())
            ->method('getTitle')
            ->will($this->returnValue('bar first'));
        $this->chain->addPlugin($plugin_first);

        /* @var $plugin_second \PHPUnit_Framework_MockObject_MockObject|Plugin */
        $plugin_second = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Plugin\Plugin');
        $plugin_second
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->will($this->returnValue('foo second'));
        $plugin_second
            ->expects($this->once())
            ->method('getTitle')
            ->will($this->returnValue('bar second'));
        $this->chain->addPlugin($plugin_second);

        $this->assertTrue($this->chain->hasPlugins());
        $this->assertEquals(['foo first', 'foo second'], $this->chain->getNames());
        $this->assertEquals(
            [
                'foo first' => 'bar first',
                'foo second' => 'bar second'
            ],
            $this->chain->getTitles()
        );
        $this->assertEquals(
            [
                'foo first' => $plugin_first,
                'foo second' => $plugin_second
            ],
            $this->chain->getPlugins()
        );

        $this->assertEquals($plugin_first, $this->chain->getPlugin('foo first'));
        $this->assertEquals($plugin_second, $this->chain->getPlugin('foo second'));
        $this->assertNull($this->chain->getPlugin('baz'));
    }
}
