<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Form\Entity;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AnimeDb\Bundle\CatalogBundle\Form\Entity\Image;
use AnimeDb\Bundle\CatalogBundle\Form\Entity\Name;
use AnimeDb\Bundle\CatalogBundle\Form\Entity\Source;
use AnimeDb\Bundle\AppBundle\Form\Field\Image as ImageField;
use AnimeDb\Bundle\AppBundle\Form\Field\LocalPath as LocalPathField;
use AnimeDb\Bundle\AppBundle\Form\Field\Rating as RatingField;
use AnimeDb\Bundle\CatalogBundle\Entity\Item as ItemEntity;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Chain;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Refiller;
use Symfony\Component\Templating\EngineInterface as TemplatingInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use AnimeDb\Bundle\AppBundle\Util\Filesystem;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

/**
 * Item form
 *
 * @package AnimeDb\Bundle\CatalogBundle\Form\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Item extends AbstractType
{
    /**
     * Refiller chain
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Chain
     */
    protected $chain;

    /**
     * Templating
     *
     * @var \Symfony\Component\Templating\EngineInterface
     */
    protected $templating;

    /**
     * Translator
     *
     * @var \Symfony\Bundle\FrameworkBundle\Translation\Translator
     */
    protected $translator;

    /**
     * Router
     *
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    protected $router;

    /**
     * Set refiller chain
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Chain $chain
     */
    public function setRefillerChain(Chain $chain)
    {
        $this->chain = $chain;
    }

    /**
     * Set templating
     *
     * @param \Symfony\Component\Templating\EngineInterface $templating
     */
    public function setTemplating(TemplatingInterface $templating)
    {
        $this->templating = $templating;
    }

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
        $this->router = $router;
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'Main name'
            ])
            ->add('names', 'collection', [
                'type'         => new Name(),
                'allow_add'    => true,
                'by_reference' => false,
                'allow_delete' => true,
                'required'     => false,
                'label'        => 'Other names',
                'options'      => [
                    'required' => false
                ],
                'attr' => $this->getRefillAttr(Refiller::FIELD_NAMES, $options['data'])
            ])
            ->add('cover', new ImageField(), [
                'required' => false
            ])
            ->add('rating', new RatingField())
            ->add('date_premiere', 'date', [
                'format' => 'yyyy-MM-dd',
                'widget' => 'single_text',
                'required' => false,
                'attr' => $this->getRefillAttr(Refiller::FIELD_DATE_PREMIERE, $options['data'])
            ])
            ->add('date_end', 'date', [
                'format' => 'yyyy-MM-dd',
                'widget' => 'single_text',
                'required' => false,
                'attr' => $this->getRefillAttr(Refiller::FIELD_DATE_END, $options['data']),
                'help' => 'Specify for completed series'
            ])
            ->add('episodes_number', null, [
                'required' => false,
                'label'    => 'Number of episodes',
                'attr' => $this->getRefillAttr(Refiller::FIELD_EPISODES_NUMBER, $options['data']),
                'help' => 'For releasing the series, you can specify the actual number of episodes with a plus at the end. Example: 123+'
            ])
            ->add('duration', null, [
                'attr' => $this->getRefillAttr(Refiller::FIELD_DURATION, $options['data'])
            ])
            ->add('type', 'entity', [
                'class'    => 'AnimeDbCatalogBundle:Type',
                'property' => 'name'
            ])
            ->add('genres', 'entity', [
                'class'    => 'AnimeDbCatalogBundle:Genre',
                'property' => 'name',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'attr' => $this->getRefillAttr(Refiller::FIELD_GENRES, $options['data'])
            ])
            ->add('labels', 'entity', [
                'class'    => 'AnimeDbCatalogBundle:Label',
                'property' => 'name',
                'multiple' => true,
                'expanded' => true,
                'required' => false
            ])
            ->add('studio', 'entity', [
                'class'    => 'AnimeDbCatalogBundle:Studio',
                'property' => 'name',
                'required' => false,
                'label' => 'Animation studio',
                'attr' => $this->getRefillAttr(Refiller::FIELD_STUDIO, $options['data'])
            ])
            ->add('country', 'entity', [
                'class'    => 'AnimeDbCatalogBundle:Country',
                'property' => 'name',
                'required' => false,
                'attr' => $this->getRefillAttr(Refiller::FIELD_COUNTRY, $options['data'])
            ])
            ->add('storage', 'entity', [
                'class'    => 'AnimeDbCatalogBundle:Storage',
                'property' => 'name',
                'required' => false,
                'attr' => [
                    'class' => 'f-storage',
                    'data-source' => $this->router->generate('storage_path'),
                    'data-target' => '#'.$this->getName().'_path'
                ]
            ])
            ->add('path', new LocalPathField(), [
                'required' => false,
                'attr' => [
                    'placeholder' => Filesystem::getUserHomeDir()
                ]
            ])
            ->add('translate', 'textarea', [
                'required' => false,
                'attr' => $this->getRefillAttr(Refiller::FIELD_TRANSLATE, $options['data']) + ['rows' => 2],
                'help' => 'Description language soundtracks and subtitles in free form'
            ])
            ->add('summary', null, [
                'required' => false,
                'attr' => $this->getRefillAttr(Refiller::FIELD_SUMMARY, $options['data'])
            ])
            ->add('episodes', null, [
                'required' => false,
                'attr' => $this->getRefillAttr(Refiller::FIELD_EPISODES, $options['data'])
            ])
            ->add('file_info', null, [
                'required' => false,
                'attr' => $this->getRefillAttr(Refiller::FIELD_FILE_INFO, $options['data'])
            ])
            ->add('sources', 'collection', [
                'type'         => new Source(),
                'allow_add'    => true,
                'by_reference' => false,
                'allow_delete' => true,
                'required'     => false,
                'label'        => 'External sources',
                'options'      => [
                    'required' => false
                ],
                'attr' => $this->getRefillAttr(Refiller::FIELD_SOURCES, $options['data'])
            ])
            ->add('images', 'collection', [
                'type'         => new Image(),
                'allow_add'    => true,
                'by_reference' => false,
                'allow_delete' => true,
                'required'     => false,
                'label'        => 'Other images',
                'options'      => [
                    'required' => false
                ],
                'attr' => $this->getRefillAttr(Refiller::FIELD_IMAGES, $options['data'])
            ])
        ;
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::setDefaultOptions()
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AnimeDb\Bundle\CatalogBundle\Entity\Item'
        ]);
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'anime_db_catalog_entity_item';
    }

    /**
     * Get the field refill attributes
     *
     * @param string $field
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item|null $item
     *
     * @return array
     */
    protected function getRefillAttr($field, ItemEntity $item = null)
    {
        // item exists and can be refilled
        if ($item instanceof ItemEntity && $item->getName() &&
            ($plugins = $this->chain->getPluginsThatCanFillItem($item, $field))
        ) {
            /* @var $plugin \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Refiller */
            foreach ($plugins as $key => $plugin) {
                $plugins[$key] = [
                    'name' => $plugin->getName(),
                    'title' => $plugin->getTitle(),
                    'can_refill' => $plugin->isCanRefill($item, $field) // can refill or can search
                ];
            }

            return [
                'data-type' => 'refill',
                'data-plugins' => $this->templating->render('AnimeDbCatalogBundle:Form:refillers.html.twig', [
                    'item' => $item,
                    'field' => $field,
                    'plugins' => $plugins
                ])
            ];
        }
        return [];
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::finishView()
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        // order
        $collator = new \Collator($this->translator->getLocale());
        $sorter = function ($a, $b) use ($collator) {
            return $collator->compare($a->vars['label'], $b->vars['label']);
        };
        usort($view->children['genres']->children, $sorter);
        usort($view->children['labels']->children, $sorter);

        $sorter = function ($a, $b) use ($collator) {
            return $collator->compare($a->label, $b->label);
        };
        usort($view->children['studio']->vars['choices'], $sorter);
        usort($view->children['country']->vars['choices'], $sorter);
        usort($view->children['storage']->vars['choices'], $sorter);
    }
}