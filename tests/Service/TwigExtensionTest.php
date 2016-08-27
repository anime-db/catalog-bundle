<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CatalogBundle\Tests\Service;

use AnimeDb\Bundle\CatalogBundle\Service\TwigExtension;

/**
 * Test twig extension.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class TwigExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \AnimeDb\Bundle\CatalogBundle\Service\TwigExtension
     */
    protected $extension;

    protected function setUp()
    {
        $this->extension = new TwigExtension();
    }

    public function testGetFilters()
    {
        $this->assertEquals(
            ['dummy' => new \Twig_Filter_Method($this->extension, 'dummy')],
            $this->extension->getFilters()
        );
    }

    public function testDummy()
    {
        $this->assertEquals('my_path', $this->extension->dummy('my_path', 'my_filter'));
    }

    public function testDummyApply()
    {
        $this->assertEquals(
            '/bundles/animedbcatalog/images/dummy/my_filter.jpg',
            $this->extension->dummy('', 'my_filter')
        );
    }

    public function testGetName()
    {
        $this->assertEquals('extension', $this->extension->getName());
    }
}
