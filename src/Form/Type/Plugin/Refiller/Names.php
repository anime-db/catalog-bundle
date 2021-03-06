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
use AnimeDb\Bundle\CatalogBundle\Form\Type\Entity\Name;

/**
 * Refill item field names.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Names extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('names', 'collection', [
                'type' => new Name(),
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
                'label' => false,
                'options' => [
                    'required' => false,
                ],
            ])
            ->add('source', 'hidden', [
                'required' => false,
                'label' => false,
            ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return Item::NAME;
    }
}
