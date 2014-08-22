<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller;

/**
 * Element search results
 * 
 * @package AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Item
{
    /**
     * Name
     *
     * @var string
     */
    protected $name = '';

    /**
     * Data for refill item
     *
     * @var array
     */
    protected $data = [];

    /**
     * Source
     *
     * Can set the source to source item to avoid the next search for this item
     *
     * @var string
     */
    protected $source = '';

    /**
     * Image
     *
     * @var string
     */
    protected $image = '';

    /**
     * Description
     *
     * @var string
     */
    protected $description = '';

    /**
     * Construct
     *
     * @param string $name
     * @param array $data
     * @param string $source
     * @param string|null $image
     * @param string|null $description
     */
    public function __construct($name, array $data, $source, $image = '', $description = '')
    {
        $this->name = $name;
        $this->data = $data;
        $this->source = $source;
        $this->image = $image;
        $this->description = $description;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get data for refill item
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}