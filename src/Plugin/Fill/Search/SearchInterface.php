<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search;

use AnimeDb\Bundle\CatalogBundle\Plugin\PluginInMenuInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use AnimeDb\Bundle\CatalogBundle\Entity\Item as EntityItem;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Search as SearchForm;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Filler;

/**
 * @package AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search
 */
interface SearchInterface extends PluginInMenuInterface
{
    /**
     * Search source by name
     *
     * @param array $data
     *
     * @return Item[]
     */
    public function search(array $data);

    /**
     * @param Router $router
     */
    public function setRouter(Router $router);

    /**
     * @return SearchForm
     */
    public function getForm();

    /**
     * @param Filler $filler
     */
    public function setFiller(Filler $filler);

    /**
     * @return Filler
     */
    public function getFiller();

    /**
     * @param mixed $data
     *
     * @return string
     */
    public function getLinkForFill($data);

    /**
     * @param string $name
     *
     * @return string
     */
    public function getLinkForSearch($name);

    /**
     * Try search item by name and fill it if can
     *
     * @param string $name
     *
     * @return EntityItem|null
     */
    public function getCatalogItem($name);
}
