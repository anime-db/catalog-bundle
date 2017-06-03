<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Form\Type\Settings;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Chain;

/**
 * General settings form.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class General extends AbstractType
{
    /**
     * @var Chain
     */
    protected $chain;

    /**
     * @param Chain $chain
     */
    public function __construct(Chain $chain)
    {
        $this->chain = $chain;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $search_choices = ['' => 'No'];
        foreach ($this->chain->getPlugins() as $plugin) {
            $search_choices[$plugin->getName()] = $plugin->getTitle();
        }

        $builder
            ->add('locale', 'locale', [
                'label' => 'Language',
            ])
            ->add('task_scheduler', 'checkbox', [
                'required' => false,
                'label' => 'Task scheduler',
                'help' => 'A separate process to perform various tasks in the background, such as checks for system '.
                    'updates',
            ])
            ->add('default_search', 'choice', [
                'required' => false,
                'choices' => $search_choices,
                'label' => 'Default search plugin',
                'help' => 'When detects a new item, the application will try to add it using the selected plugin in '.
                    'the first place. If you leave the field blank then the selection of plugins will be carried out '.
                    'in alphabetical order.',
            ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'settings_general';
    }
}
