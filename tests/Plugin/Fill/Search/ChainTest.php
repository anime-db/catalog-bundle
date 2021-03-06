<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Plugin\Fill\Search;

use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Chain;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\SearchInterface;

/**
 * Test search chain.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function getDafeultPlugins()
    {
        return [
            [''],
            ['foo'],
            ['bar'],
        ];
    }

    /**
     * @dataProvider getDafeultPlugins
     *
     * @param string $dafeult_plugin
     */
    public function testGetDafeultPlugin($dafeult_plugin)
    {
        /* @var $plugin \PHPUnit_Framework_MockObject_MockObject|SearchInterface */
        $plugin = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\SearchInterface');
        $plugin
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->will($this->returnValue('foo'));

        $chain = new Chain($dafeult_plugin);
        $chain->addPlugin($plugin);

        if ($dafeult_plugin == 'foo') {
            $this->assertEquals($plugin, $chain->getDafeultPlugin());
        } else {
            $this->assertNull($chain->getDafeultPlugin());
        }
    }
}
