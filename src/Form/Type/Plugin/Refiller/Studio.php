<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Refiller;

use AnimeDb\Bundle\CatalogBundle\Form\Type\Entity\Item;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use AnimeDb\Bundle\CatalogBundle\Form\ViewSorter;

/**
 * Refill item field studio.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Studio extends AbstractType
{
    /**
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
            ->add('studio', 'entity', [
                'class' => 'AnimeDbCatalogBundle:Studio',
                'property' => 'name',
                'label' => false,
            ])
            ->add('source', 'hidden', [
                'required' => false,
                'label' => false,
            ]);
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $this->sorter->choice($view->children['studio']);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return Item::NAME;
    }
}
