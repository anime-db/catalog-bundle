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

/**
 * Refill item field date premiere
 *
 * @package AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Refiller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class DatePremiere extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date_premiere', 'date', [
                'format' => 'yyyy-MM-dd',
                'widget' => 'single_text',
                'label'    => false
            ])
            ->add('source', 'hidden', [
                'required' => false,
                'label'    => false
            ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'anime_db_catalog_entity_item';
    }
}
