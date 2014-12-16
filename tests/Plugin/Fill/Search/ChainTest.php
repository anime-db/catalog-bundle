<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Plugin\Fill\Search;

use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Chain;

/**
 * Test search chain
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Plugin\Fill\Search
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Get dafeult plugins
     *
     * @return array
     */
    public function getDafeultPlugins()
    {
        return [
            [''],
            ['foo'],
            ['bar']
        ];
    }

    /**
     * Test get dafeult plugin
     *
     * @dataProvider getDafeultPlugins
     *
     * @param string $dafeult_plugin
     */
    public function testGetDafeultPlugin($dafeult_plugin)
    {
        $plugin = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Search');
        $plugin
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('foo');

        $chain = new Chain($dafeult_plugin);
        $chain->addPlugin($plugin);

        if ($dafeult_plugin == 'foo') {
            $this->assertEquals($plugin, $chain->getDafeultPlugin());
        } else {
            $this->assertNull($chain->getDafeultPlugin());
        }
    }
}
