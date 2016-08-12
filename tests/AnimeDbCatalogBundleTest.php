<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CatalogBundle\Tests;

use AnimeDb\Bundle\CatalogBundle\AnimeDbCatalogBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Test bundle.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class AnimeDbCatalogBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $that = $this;
        /* @var $container \PHPUnit_Framework_MockObject_MockObject|ContainerBuilder */
        $container = $this
            ->getMockBuilder('\Symfony\Component\DependencyInjection\ContainerBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $container
            ->expects($this->at(0))
            ->method('addCompilerPass')
            ->will($this->returnCallback(function ($pass) use ($that) {
                $that->assertInstanceOf('\AnimeDb\Bundle\CatalogBundle\DependencyInjection\Compiler\PluginPass', $pass);
            }));
        $container
            ->expects($this->at(1))
            ->method('addCompilerPass')
            ->will($this->returnCallback(function ($pass) use ($that) {
                $that->assertInstanceOf('\AnimeDb\Bundle\CatalogBundle\DependencyInjection\Compiler\InstallItemPass', $pass);
            }));

        $bundle = new AnimeDbCatalogBundle();
        $bundle->build($container);
    }
}
