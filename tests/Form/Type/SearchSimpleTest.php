<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Form\Type;

use AnimeDb\Bundle\CatalogBundle\Form\Type\SearchSimple;

/**
 * Test form SearchSimple
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Form\Type
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class SearchSimpleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Get sources
     *
     * @return array
     */
    public function getSources()
    {
        return [
            [''],
            ['foo'],
        ];
    }

    /**
     * Test build form
     *
     * @dataProvider getSources
     *
     * @param unknown $source
     */
    public function testBuildForm($source)
    {
        $form = new SearchSimple($source);
        $builder = $this->getMock('\Symfony\Component\Form\FormBuilderInterface');
        $builder
            ->expects($this->once())
            ->method('add')
            ->with('name', 'search', [
                'label' => 'Name',
                'required' => false,
                'attr' => $source ? ['data-source' => $source] : []
            ]);
        $form->buildForm($builder, []);
    }

    /**
     * Test get name
     */
    public function testGetName()
    {
        $form = new SearchSimple();
        $this->assertEquals('anime_db_catalog_search_items', $form->getName());
    }
}
