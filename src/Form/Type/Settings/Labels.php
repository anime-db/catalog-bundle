<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Form\Type\Settings;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Entity\Label;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use AnimeDb\Bundle\CatalogBundle\Form\ViewSorter;

/**
 * Labels form.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Labels extends AbstractType
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
            ->add('labels', 'collection', [
                'type' => new Label(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => false,
                'label' => false,
                'options' => [
                    'required' => false,
                ],
            ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'settings_labels';
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $this->sorter->choice($view->children['labels']);
    }
}
