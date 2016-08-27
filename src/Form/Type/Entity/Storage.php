<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CatalogBundle\Form\Type\Entity;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AnimeDb\Bundle\AppBundle\Form\Type\Field\LocalPath as LocalPathField;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage as StorageEntity;
use AnimeDb\Bundle\AppBundle\Util\Filesystem;

/**
 * Storage form.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Storage extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'Name',
            ])
            ->add('path', new LocalPathField(), [
                'label' => 'Path',
                'required' => false,
                'attr' => [
                    'placeholder' => Filesystem::getUserHomeDir(),
                ],
            ])
            ->add('type', 'choice', [
                'choices' => StorageEntity::getTypeTitles(),
                'label' => 'Type',
            ])
            ->add('description', null, [
                'label' => 'Description',
            ]);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AnimeDb\Bundle\CatalogBundle\Entity\Storage',
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'entity_storage';
    }
}
