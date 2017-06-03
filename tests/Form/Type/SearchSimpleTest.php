<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CatalogBundle\Tests\Form\Type;

use AnimeDb\Bundle\CatalogBundle\Form\Type\SearchSimple;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Test form SearchSimple.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class SearchSimpleTest extends \PHPUnit_Framework_TestCase
{
    /**
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
     * @dataProvider getSources
     *
     * @param string $source
     */
    public function testBuildForm($source)
    {
        $form = new SearchSimple($source);
        /* @var $builder \PHPUnit_Framework_MockObject_MockObject|FormBuilderInterface */
        $builder = $this->getMock('\Symfony\Component\Form\FormBuilderInterface');
        $builder
            ->expects($this->once())
            ->method('add')
            ->with('name', 'text', [
                'label' => 'Name',
                'required' => false,
                'attr' => $source ? ['data-source' => $source] : [],
            ]);
        $form->buildForm($builder, []);
    }

    public function testGetName()
    {
        $form = new SearchSimple();
        $this->assertEquals('search', $form->getName());
    }
}
