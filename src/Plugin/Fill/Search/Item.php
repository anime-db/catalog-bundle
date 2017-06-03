<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search;

/**
 * Element search results.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Item
{
    /**
     * Name.
     *
     * @var string
     */
    protected $name = '';

    /**
     * Link to fill item from source.
     *
     * @var string
     */
    protected $link = '';

    /**
     * Source.
     *
     * Can set the source to source item to avoid the next search for this item
     *
     * @var string
     */
    protected $source = '';

    /**
     * Image.
     *
     * @var string
     */
    protected $image = '';

    /**
     * Description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Construct.
     *
     * @param string $name
     * @param string $link
     * @param string $image
     * @param string $description
     * @param string|null $source
     */
    public function __construct($name, $link, $image, $description, $source = '')
    {
        $this->name = $name;
        $this->link = $link;
        $this->image = $image;
        $this->description = $description;
        $this->source = $source;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get link to fill item from source.
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Get source.
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Get image.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
