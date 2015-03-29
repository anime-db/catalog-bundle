<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Form\Type\Install;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Chain;

/**
 * Settings for installation page
 *
 * @package AnimeDb\Bundle\CatalogBundle\Form\Type\Install
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Settings extends AbstractType
{
    /**
     * Request
     *
     * @var \Symfony\Component\HttpFoundation\Request|null
     */
    protected $request;

    /**
     * Search chain
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Plugin\Search\Filler\Chain
     */
    protected $chain;

    /**
     * Construct
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Chain $cain
     */
    public function __construct(Chain $cain)
    {
        $this->chain = $cain;
    }

    /**
     * Set request
     *
     * @param \Symfony\Component\HttpFoundation\Request|null $request
     */
    public function setRequest(Request $request = null)
    {
        if ($request) {
            $this->request = $request;
        }
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $search_choices = ['' => 'No'];
        /* @var $plugin \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Search */
        foreach ($this->chain->getPlugins() as $plugin) {
            $search_choices[$plugin->getName()] = $plugin->getTitle();
        }

        $builder
            ->add('locale', 'locale', [
                'label' => 'Language',
                'data' => $this->request ? $this->request->getLocale() : ''
            ])
            ->add('default_search', 'choice', [
                'required' => false,
                'choices' => $search_choices,
                'label' => 'Default search plugin'
            ]);
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'anime_db_catalog_install_settings';
    }
}
