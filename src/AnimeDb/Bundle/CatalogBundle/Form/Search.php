<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Search items form
 *
 * @package AnimeDb\Bundle\CatalogBundle\Form
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Search extends AbstractType
{
    /**
     * Autocomplete source
     *
     * @var string|null
     */
    private $source;

    /**
     * Construct
     *
     * @param string|null $source
     */
    public function __construct($source = null)
    {
        $this->source = $source;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('GET')
            ->add('name', 'search', [
                'label' => 'Name',
                'required' => false,
                'attr' => $this->source ? ['data-source' => $this->source] : []
            ])
            ->add('date_add', 'date', [
                'label' => 'Date added',
                'format' => 'yyyy-MM-dd',
                'widget' => 'single_text',
                'required' => false,
                'help' => 'Will select all records starting with the specified date'
            ])
            ->add('date_start', 'date', [
                'format' => 'yyyy-MM-dd',
                'widget' => 'single_text',
                'required' => false,
                'help' => 'Will select all records starting with the specified date'
            ])
            ->add('date_end', 'date', [
                'format' => 'yyyy-MM-dd',
                'widget' => 'single_text',
                'required' => false,
                'help' => 'Will select all records ending with a specified date'
            ])
            ->add('type', 'entity', [
                'class'    => 'AnimeDbCatalogBundle:Type',
                'query_builder' => function (EntityRepository $rep) {
                    return $rep->createQueryBuilder('t')->innerJoin('t.items', 'i');
                },
                'property' => 'name',
                'required' => false
            ])
            ->add('genres', 'entity', [
                'class'    => 'AnimeDbCatalogBundle:Genre',
                'query_builder' => function (EntityRepository $rep) {
                    return $rep->createQueryBuilder('g')->innerJoin('g.items', 'i');
                },
                'property' => 'name',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'help' => 'Select multiple genres to narrow your search'
            ])
            ->add('studio', 'entity', [
                'class'    => 'AnimeDbCatalogBundle:Studio',
                'query_builder' => function (EntityRepository $rep) {
                    return $rep->createQueryBuilder('s')->innerJoin('s.items', 'i');
                },
                'property' => 'name',
                'required' => false
            ])
            ->add('country', 'entity', [
                'class'    => 'AnimeDbCatalogBundle:Country',
                'query_builder' => function (EntityRepository $rep) {
                    return $rep->createQueryBuilder('c')->innerJoin('c.items', 'i');
                },
                'property' => 'name',
                'required' => false
            ])
            ->add('storage', 'entity', [
                'class'    => 'AnimeDbCatalogBundle:Storage',
                'query_builder' => function (EntityRepository $rep) {
                    return $rep->createQueryBuilder('s')->innerJoin('s.items', 'i');
                },
                'property' => 'name',
                'required' => false
            ]);
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::setDefaultOptions()
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AnimeDb\Bundle\CatalogBundle\Entity\Search'
        ]);
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'anime_db_catalog_search_items';
    }
}