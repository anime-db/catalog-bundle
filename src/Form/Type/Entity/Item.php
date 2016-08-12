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
use AnimeDb\Bundle\AppBundle\Form\Type\Field\Image as ImageField;
use AnimeDb\Bundle\AppBundle\Form\Type\Field\LocalPath as LocalPathField;
use AnimeDb\Bundle\AppBundle\Form\Type\Field\Rating as RatingField;
use AnimeDb\Bundle\CatalogBundle\Entity\Item as ItemEntity;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Chain;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\RefillerInterface;
use AnimeDb\Bundle\CatalogBundle\Form\ViewSorter;
use Symfony\Component\Templating\EngineInterface as TemplatingInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use AnimeDb\Bundle\AppBundle\Util\Filesystem;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * Item form.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Item extends AbstractType
{
    /**
     * Refiller chain.
     *
     * @var Chain
     */
    protected $chain;

    /**
     * @var TemplatingInterface
     */
    protected $templating;

    /**
     * View sorter.
     *
     * @var ViewSorter
     */
    protected $sorter;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @param Chain $chain
     */
    public function setRefillerChain(Chain $chain)
    {
        $this->chain = $chain;
    }

    /**
     * @param TemplatingInterface $templating
     */
    public function setTemplating(TemplatingInterface $templating)
    {
        $this->templating = $templating;
    }

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
        $this->router = $router;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'Main name',
            ])
            ->add('names', 'collection', [
                'type' => new Name(),
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
                'required' => false,
                'label' => 'Other names',
                'options' => [
                    'required' => false,
                ],
                'attr' => $this->getRefillAttr(RefillerInterface::FIELD_NAMES, $options['data']),
            ])
            ->add('cover', new ImageField(), [
                'required' => false,
            ])
            ->add('rating', new RatingField())
            ->add('date_premiere', 'date', [
                'format' => 'yyyy-MM-dd',
                'widget' => 'single_text',
                'required' => false,
                'attr' => $this->getRefillAttr(RefillerInterface::FIELD_DATE_PREMIERE, $options['data']),
            ])
            ->add('date_end', 'date', [
                'format' => 'yyyy-MM-dd',
                'widget' => 'single_text',
                'required' => false,
                'attr' => $this->getRefillAttr(RefillerInterface::FIELD_DATE_END, $options['data']),
                'help' => 'Specify for completed series',
            ])
            ->add('episodes_number', null, [
                'required' => false,
                'label' => 'Number of episodes',
                'attr' => $this->getRefillAttr(RefillerInterface::FIELD_EPISODES_NUMBER, $options['data']),
                'help' => 'For releasing the series, you can specify the actual number of episodes with a plus at the end. Example: 123+',
            ])
            ->add('duration', null, [
                'attr' => $this->getRefillAttr(RefillerInterface::FIELD_DURATION, $options['data']),
            ])
            ->add('type', 'entity', [
                'class' => 'AnimeDbCatalogBundle:Type',
                'property' => 'name',
            ])
            ->add('genres', 'entity', [
                'class' => 'AnimeDbCatalogBundle:Genre',
                'property' => 'name',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'attr' => $this->getRefillAttr(RefillerInterface::FIELD_GENRES, $options['data']),
            ])
            ->add('labels', 'entity', [
                'class' => 'AnimeDbCatalogBundle:Label',
                'property' => 'name',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
            ->add('studio', 'entity', [
                'class' => 'AnimeDbCatalogBundle:Studio',
                'property' => 'name',
                'required' => false,
                'label' => 'Animation studio',
                'attr' => $this->getRefillAttr(RefillerInterface::FIELD_STUDIO, $options['data']),
            ])
            ->add('country', 'entity', [
                'class' => 'AnimeDbCatalogBundle:Country',
                'property' => 'name',
                'required' => false,
                'attr' => $this->getRefillAttr(RefillerInterface::FIELD_COUNTRY, $options['data']),
            ])
            ->add('storage', 'entity', [
                'class' => 'AnimeDbCatalogBundle:Storage',
                'property' => 'name',
                'required' => false,
                'attr' => [
                    'class' => 'f-storage',
                    'data-source' => $this->router->generate('storage_path'),
                    'data-target' => '#'.$this->getName().'_path',
                ],
            ])
            ->add('path', new LocalPathField(), [
                'required' => false,
                'attr' => [
                    'placeholder' => Filesystem::getUserHomeDir(),
                ],
            ])
            ->add('translate', 'textarea', [
                'required' => false,
                'attr' => $this->getRefillAttr(RefillerInterface::FIELD_TRANSLATE, $options['data']) + ['rows' => 2],
                'help' => 'Description language soundtracks and subtitles in free form',
            ])
            ->add('summary', null, [
                'required' => false,
                'attr' => $this->getRefillAttr(RefillerInterface::FIELD_SUMMARY, $options['data']),
            ])
            ->add('episodes', null, [
                'required' => false,
                'attr' => $this->getRefillAttr(RefillerInterface::FIELD_EPISODES, $options['data']),
            ])
            ->add('file_info', null, [
                'required' => false,
                'attr' => $this->getRefillAttr(RefillerInterface::FIELD_FILE_INFO, $options['data']),
            ])
            ->add('sources', 'collection', [
                'type' => new Source(),
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
                'required' => false,
                'label' => 'External sources',
                'options' => [
                    'required' => false,
                ],
                'attr' => $this->getRefillAttr(RefillerInterface::FIELD_SOURCES, $options['data']),
            ])
            ->add('images', 'collection', [
                'type' => new Image(),
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
                'required' => false,
                'label' => 'Other images',
                'options' => [
                    'required' => false,
                ],
                'attr' => $this->getRefillAttr(RefillerInterface::FIELD_IMAGES, $options['data']),
            ])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AnimeDb\Bundle\CatalogBundle\Entity\Item',
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'anime_db_catalog_entity_item';
    }

    /**
     * Get the field refill attributes.
     *
     * @param string $field
     * @param ItemEntity|null $item
     *
     * @return array
     */
    protected function getRefillAttr($field, ItemEntity $item = null)
    {
        // item exists and can be refilled
        if ($item instanceof ItemEntity && $item->getName() &&
            ($plugins = $this->chain->getPluginsThatCanFillItem($item, $field))
        ) {
            /* @var $plugin RefillerInterface */
            foreach ($plugins as $key => $plugin) {
                $plugins[$key] = [
                    'name' => $plugin->getName(),
                    'title' => $plugin->getTitle(),
                    'can_refill' => $plugin->isCanRefill($item, $field), // can refill or can search
                ];
            }

            return [
                'data-type' => 'refill',
                'data-plugins' => $this->templating->render(
                    'AnimeDbCatalogBundle:Form:refillers.html.twig',
                    [
                        'item' => $item,
                        'field' => $field,
                        'plugins' => $plugins,
                    ]
                ),
            ];
        }

        return [];
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
        $this->sorter->choice($view->children['sources']);
    }
}
