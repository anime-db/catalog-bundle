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
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
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
    protected $source;

    /**
     * Translator
     *
     * @var \Symfony\Bundle\FrameworkBundle\Translation\Translator
     */
    protected $translator;

    /**
     * Set translator
     *
     * @param \Symfony\Bundle\FrameworkBundle\Translation\Translator $translator
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Set router
     *
     * @param \Symfony\Bundle\FrameworkBundle\Routing\Router $router
     */
    public function setRouter(Router $router)
    {
        $this->source = $router->generate('home_autocomplete_name');
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
                'help' => 'Will select all records added with the specified date'
            ])
            ->add('date_premiere', 'date', [
                'format' => 'yyyy-MM-dd',
                'widget' => 'single_text',
                'required' => false,
                'help' => 'Will select all records starting with the specified date'
            ])
            ->add('date_end', 'date', [
                'format' => 'yyyy-MM-dd',
                'widget' => 'single_text',
                'required' => false,
                'help' => 'Will select all records ending in the specified date'
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
     * @see \Symfony\Component\Form\AbstractType::finishView()
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        // order
        $collator = new \Collator($this->translator->getLocale());
        usort($view->children['genres']->children, function ($a, $b) use ($collator) {
            return $collator->compare($a->vars['label'], $b->vars['label']);
        });

        $sort_field = function ($a, $b) use ($collator) {
            return $collator->compare($a->label, $b->label);
        };
        usort($view->children['studio']->vars['choices'], $sort_field);
        usort($view->children['country']->vars['choices'], $sort_field);
        usort($view->children['storage']->vars['choices'], $sort_field);
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