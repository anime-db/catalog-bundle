<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Form\Plugin\Refiller;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Refill item field studio
 *
 * @package AnimeDb\Bundle\CatalogBundle\Form\Plugin\Refiller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Studio extends AbstractType
{
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
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('studio', 'entity', [
                'class'    => 'AnimeDbCatalogBundle:Studio',
                'property' => 'name',
                'label'    => false
            ])
            ->add('source', 'hidden', [
                'required' => false,
                'label'    => false
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
        usort($view->children['studio']->vars['choices'], function ($a, $b) use ($collator) {
            return $collator->compare($a->label, $b->label);
        });
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'anime_db_catalog_entity_item';
    }
}
