<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Service;

use AnimeDb\Bundle\CatalogBundle\Service\TwigExtension;

/**
 * Test bundle
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Service
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class TwigExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Extension
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Service\TwigExtension
     */
    protected $extension;

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->extension = new TwigExtension();
    }

    /**
     * Test get filters
     */
    public function testGetFilters()
    {
        $this->assertEquals(
            ['dummy' => new \Twig_Filter_Method($this->extension, 'dummy')],
            $this->extension->getFilters()
        );
    }

    /**
     * Test dummy
     */
    public function testDummy()
    {
        $this->assertEquals('my_path', $this->extension->dummy('my_path', 'my_filter'));
    }

    /**
     * Test dummy apply
     */
    public function testDummyApply()
    {
        $this->assertEquals(
            '/bundles/animedbcatalog/images/dummy/my_filter.jpg',
            $this->extension->dummy('', 'my_filter')
        );
    }

    /**
     * Test get name
     */
    public function testGetName()
    {
        $this->assertEquals('anime_db_catalog_extension', $this->extension->getName());
    }
}
