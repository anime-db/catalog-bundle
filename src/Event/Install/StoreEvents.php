<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CatalogBundle\Event\Install;

/**
 * Install event names.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
final class StoreEvents
{
    /**
     * Event thrown when a installed application.
     *
     * @var string
     */
    const INSTALL_APP = 'anime_db.install.app';

    /**
     * Event thrown when a install samples.
     *
     * @var string
     */
    const INSTALL_SAMPLES = 'anime_db.install.samples';
}
