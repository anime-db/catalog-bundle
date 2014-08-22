<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Form\Settings;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AnimeDb\Bundle\CatalogBundle\Form\Entity\Label;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

/**
 * labels form
 *
 * @package AnimeDb\Bundle\CatalogBundle\Form\Settings
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Labels extends AbstractType
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
            ->add('labels', 'collection', [
                'type'         => new Label(),
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required'     => false,
                'label'        => false,
                'options'      => [
                    'required' => false
                ]
            ]);
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'anime_db_catalog_labels';
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
            return $collator->compare($a->vars['value']->getName(), $b->vars['value']->getName());
        };
        usort($view->children['labels']->children, $sorter);
    }
}