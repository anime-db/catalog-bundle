<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\DependencyInjection\Compiler;

use AnimeDb\Bundle\CatalogBundle\DependencyInjection\Compiler\PluginPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Test plugin pass
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\DependencyInjection\Compiler
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class PluginPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ContainerBuilder
     */
    protected $container;

    /**
     * @var \AnimeDb\Bundle\CatalogBundle\DependencyInjection\Compiler\PluginPass
     */
    protected $compiler;

    /**
     * Chains
     *
     * @var array
     */
    protected $chains = [
        'anime_db.plugin.filler' => 'anime_db.filler',
        'anime_db.plugin.search_fill' => 'anime_db.search',
        'anime_db.plugin.refiller' => 'anime_db.refiller',
        'anime_db.plugin.import' => 'anime_db.import',
        'anime_db.plugin.export' => 'anime_db.export',
        'anime_db.plugin.item' => 'anime_db.item',
        'anime_db.plugin.setting' => 'anime_db.setting'
    ];

    protected function setUp()
    {
        $this->container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerBuilder');
        $this->compiler = new PluginPass();
    }

    public function testProcessFail()
    {
        $chain_names = array_keys($this->chains);
        foreach ($chain_names as $i => $chain_name) {
            $this->container
                ->expects($this->at($i))
                ->method('has')
                ->with($chain_name)
                ->will($this->returnValue(false));
        }

        $this->compiler->process($this->container);
    }

    public function testProcess()
    {
        $that = $this;
        foreach (array_keys($this->chains) as $i => $chain_name) {
            $services = [
                $i+1 => [],
                $i+2 => [],
            ];
            $definition = $this->getMock('\Symfony\Component\DependencyInjection\Definition');
            $this->container
                ->expects($this->at($i*3))
                ->method('has')
                ->with($chain_name)
                ->will($this->returnValue(true));
            $this->container
                ->expects($this->at(($i*3)+1))
                ->method('findDefinition')
                ->with($chain_name)
                ->will($this->returnValue($definition));
            $this->container
                ->expects($this->at(($i*3)+2))
                ->method('findTaggedServiceIds')
                ->will($this->returnValue($services))
                ->with($this->chains[$chain_name]);
            foreach (array_keys($services) as $j => $id) {
                $definition
                    ->expects($this->at($j))
                    ->method('addMethodCall')
                    ->will($this->returnCallback(function ($method, $reference) use ($that, $id) {
                        $that->assertInternalType('array', $reference);
                        $that->assertInstanceOf('\Symfony\Component\DependencyInjection\Reference', $reference[0]);
                        $that->assertEquals($id, (string)$reference[0]);
                    }))
                    ->with('addPlugin');
            }
        }

        $this->compiler->process($this->container);
    }
}
