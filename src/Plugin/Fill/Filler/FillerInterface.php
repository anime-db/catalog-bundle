<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler;

use AnimeDb\Bundle\CatalogBundle\Entity\Item;
use AnimeDb\Bundle\CatalogBundle\Plugin\PluginInMenuInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Item as ItemSearch;

/**
 * Interface FillerInterface
 * @package AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler
 */
interface FillerInterface extends PluginInMenuInterface
{
    /**
     * Fill item from source
     *
     * @param array $data
     *
     * @return Item|null
     */
    public function fill(array $data);

    /**
     * @return Filler
     */
    public function getForm();

    /**
     * @param Router $router
     */
    public function setRouter(Router $router);

    /**
     * @throws \LogicException
     *
     * @param mixed $data
     *
     * @return string
     */
    public function getLinkForFill($data);

    /**
     * Fill from search result
     *
     * @param ItemSearch $item
     *
     * @return Item|null
     */
    public function fillFromSearchResult(ItemSearch $item);

    /**
     * @param string $url
     *
     * @return bool
     */
    public function isSupportedUrl($url);
}
