<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Refiller;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use AnimeDb\Bundle\CatalogBundle\Form\ViewSorter;

/**
 * Refill item field gengres
 *
 * @package AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Refiller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Gengres extends AbstractType
{
    /**
     * View sorter
     *
     * @var ViewSorter
     */
    protected $sorter;

    /**
     * @param ViewSorter $sorter
     */
    public function setViewSorter(ViewSorter $sorter)
    {
        $this->sorter = $sorter;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('genres', 'entity', [
                'class'    => 'AnimeDbCatalogBundle:Genre',
                'property' => 'name',
                'multiple' => true,
                'expanded' => true,
                'label'    => false
            ])
            ->add('source', 'hidden', [
                'required' => false,
                'label'    => false
            ]);
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $this->sorter->choice($view->children['genres']);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'anime_db_catalog_entity_item';
    }
}
