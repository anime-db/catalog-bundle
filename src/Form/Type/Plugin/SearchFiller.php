<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Search filler by URL.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class SearchFiller extends AbstractType
{
    /**
     * Form name.
     *
     * @var string
     */
    const FORM_NAME = 'plugin_filler_search';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url', 'text', [
                'label' => 'URL address',
                'attr' => [
                    'placeholder' => 'http://',
                ],
            ])
            ->setMethod('GET');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => 'AnimeDb\Bundle\CatalogBundle\Entity\SearchFiller',
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::FORM_NAME;
    }
}
