<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Search simple form for home page
 *
 * @package AnimeDb\Bundle\CatalogBundle\Form\Type
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class SearchSimple extends AbstractType
{
    /**
     * Autocomplete source
     *
     * @var string
     */
    private $source;

    /**
     * Construct
     *
     * @param string $source
     */
    public function __construct($source = '')
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
            ->add('name', 'search', [
                'label' => 'Name',
                'required' => false,
                'attr' => $this->source ? ['data-source' => $this->source] : []
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