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

/**
 * Select locale on install
 *
 * @package AnimeDb\Bundle\CatalogBundle\Form\Type\Install
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Locale extends AbstractType
{
    /**
     * Request
     *
     * @var \Symfony\Component\HttpFoundation\Request|null
     */
    protected $request;

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
        $builder
            ->add('locale', 'locale', [
                'label' => 'Language',
                'data' => $this->request ? $this->request->getLocale() : ''
            ]);
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'anime_db_catalog_install_locale';
    }
}
