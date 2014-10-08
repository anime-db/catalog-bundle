<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Form\Plugin\Refiller;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use AnimeDb\Bundle\CatalogBundle\Form\ViewSorter;

/**
 * Refill item field studio
 *
 * @package AnimeDb\Bundle\CatalogBundle\Form\Plugin\Refiller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Studio extends AbstractType
{
    /**
     * View sorter
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Form\ViewSorter
     */
    protected $sorter;

    /**
     * Set view sorter
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Form\ViewSorter $sorter
     */
    public function setViewSorter(ViewSorter $sorter)
    {
        $this->sorter = $sorter;
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('studio', 'entity', [
                'class'    => 'AnimeDbCatalogBundle:Studio',
                'property' => 'name',
                'label'    => false
            ])
            ->add('source', 'hidden', [
                'required' => false,
                'label'    => false
            ]);
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::finishView()
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $this->sorter->choice($view->children['studio']);
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'anime_db_catalog_entity_item';
    }
}
