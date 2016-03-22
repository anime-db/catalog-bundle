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

use AnimeDb\Bundle\CatalogBundle\Plugin\Import\Import;

/**
 * Test plugin import
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Plugin
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ImportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Plugin
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $plugin;

    protected function setUp()
    {
        $this->plugin = $this->getMockForAbstractClass('\AnimeDb\Bundle\CatalogBundle\Plugin\Import\Import');
    }

    /**
     * Test build menu
     */
    public function testBuildMenu()
    {
        $item = $this->getMock('\Knp\Menu\ItemInterface');
        $item
            ->expects($this->once())
            ->method('addChild')
            ->with(
                'foo',
                [
                    'route' => 'item_import',
                    'routeParameters' => ['plugin' => 'bar']
                ]
            );
        $this->plugin
            ->expects($this->once())
            ->method('getTitle')
            ->will($this->returnValue('foo'));
        $this->plugin
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('bar'));

        $this->plugin->buildMenu($item);
    }
}
