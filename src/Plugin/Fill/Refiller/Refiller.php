<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller;

use AnimeDb\Bundle\CatalogBundle\Plugin\Plugin;

/**
 * @deprecated use RefillerInterface
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Refiller extends Plugin implements RefillerInterface
{
    /**
     * @var array
     */
    public static $field_names = [
        self::FIELD_COUNTRY,
        self::FIELD_DATE_END,
        self::FIELD_DATE_PREMIERE,
        self::FIELD_DURATION,
        self::FIELD_EPISODES,
        self::FIELD_EPISODES_NUMBER,
        self::FIELD_FILE_INFO,
        self::FIELD_GENRES,
        self::FIELD_IMAGES,
        self::FIELD_NAMES,
        self::FIELD_SOURCES,
        self::FIELD_STUDIO,
        self::FIELD_SUMMARY,
        self::FIELD_TRANSLATE,
    ];
}
