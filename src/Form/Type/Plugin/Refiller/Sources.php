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
use AnimeDb\Bundle\CatalogBundle\Form\Type\Entity\Source;

/**
 * Refill item field sources.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Sources extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sources', 'collection', [
                'type' => new Source(),
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
