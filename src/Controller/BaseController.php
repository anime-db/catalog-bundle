<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CatalogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper;

/**
 * BaseController.
 */
abstract class BaseController extends Controller
{
    /**
     * @return Keeper
     */
    protected function getCacheTimeKeeper()
    {
        return $this->get('cache_time_keeper');
    }
}
