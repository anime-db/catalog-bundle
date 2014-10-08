<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
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
 * Labels form
 *
 * @package AnimeDb\Bundle\CatalogBundle\Form\Type\Settings
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Labels extends AbstractType
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
            ->add('labels', 'collection', [
                'type'         => new Label(),
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required'     => false,
                'label'        => false,
                'options'      => [
                    'required' => false
                ]
            ]);
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'anime_db_catalog_labels';
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::finishView()
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $this->sorter->choice($view->children['labels']);
    }
}
