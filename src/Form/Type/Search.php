<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Doctrine\ORM\EntityRepository;
use AnimeDb\Bundle\CatalogBundle\Form\ViewSorter;

/**
 * Search items form.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Search extends AbstractType
{
    /**
     * Autocomplete source.
     *
     * @var string|null
     */
    protected $source;

    /**
     * View sorter.
     *
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
     * @param Router $router
     */
    public function setRouter(Router $router)
    {
        $this->source = $router->generate('home_autocomplete_name');
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', [
                'label' => 'Name',
                'required' => false,
                'attr' => $this->source ? ['data-source' => $this->source] : [],
            ])
            ->add('date_add', 'date', [
                'label' => 'Date added',
                'format' => 'yyyy-MM-dd',
                'widget' => 'single_text',
                'required' => false,
                'help' => 'Will select all records added with the specified date',
            ])
            ->add('date_premiere', 'date', [
                'format' => 'yyyy-MM-dd',
                'widget' => 'single_text',
                'required' => false,
                'help' => 'Will select all records starting with the specified date',
            ])
            ->add('date_end', 'date', [
                'format' => 'yyyy-MM-dd',
                'widget' => 'single_text',
                'required' => false,
                'help' => 'Will select all records ending in the specified date',
            ])
            ->add('type', 'entity', [
                'class' => 'AnimeDbCatalogBundle:Type',
                'query_builder' => function (EntityRepository $rep) {
                    return $rep->createQueryBuilder('t')->innerJoin('t.items', 'i');
                },
                'property' => 'name',
                'required' => false,
            ])
            ->add('genres', 'entity', [
                'class' => 'AnimeDbCatalogBundle:Genre',
                'query_builder' => function (EntityRepository $rep) {
                    return $rep->createQueryBuilder('g')->innerJoin('g.items', 'i');
                },
                'property' => 'name',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'help' => 'Select multiple genres to narrow your search',
            ])
            ->add('studio', 'entity', [
                'class' => 'AnimeDbCatalogBundle:Studio',
                'query_builder' => function (EntityRepository $rep) {
                    return $rep->createQueryBuilder('s')->innerJoin('s.items', 'i');
                },
                'property' => 'name',
                'required' => false,
            ])
            ->add('labels', 'entity', [
                'class' => 'AnimeDbCatalogBundle:Label',
                'query_builder' => function (EntityRepository $rep) {
                    return $rep->createQueryBuilder('l')->innerJoin('l.items', 'i');
                },
                'property' => 'name',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'help' => 'Select multiple labels to narrow your search',
            ])
            ->add('country', 'entity', [
                'class' => 'AnimeDbCatalogBundle:Country',
                'query_builder' => function (EntityRepository $rep) {
                    return $rep->createQueryBuilder('c')->innerJoin('c.items', 'i');
                },
                'property' => 'name',
                'required' => false,
            ])
            ->add('storage', 'entity', [
                'class' => 'AnimeDbCatalogBundle:Storage',
                'query_builder' => function (EntityRepository $rep) {
                    return $rep->createQueryBuilder('s')->innerJoin('s.items', 'i');
                },
                'property' => 'name',
                'required' => false,
            ])
            ->setMethod('GET');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AnimeDb\Bundle\CatalogBundle\Entity\Search',
            'csrf_protection' => false,
        ]);
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        // sort choices
        $this->sorter->choice($view->children['genres']);
        $this->sorter->choice($view->children['labels']);
        $this->sorter->choice($view->children['studio']);
        $this->sorter->choice($view->children['country']);
        $this->sorter->choice($view->children['storage']);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'animedb_catalog_search';
    }
}
