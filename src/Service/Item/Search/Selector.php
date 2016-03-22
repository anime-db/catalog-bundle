<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Service\Item\Search;

use Doctrine\Bundle\DoctrineBundle\Registry;
use AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Selector\Builder;

/**
 * Search selector
 *
 * @package AnimeDb\Bundle\CatalogBundle\Service\Item\Search
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Selector
{
    /**
     * @var Registry
     */
    protected $doctrine;

    /**
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @return Builder
     */
    public function create()
    {
        return new Builder($this->doctrine);
    }
}
