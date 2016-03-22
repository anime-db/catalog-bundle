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

use AnimeDb\Bundle\CatalogBundle\Plugin\Plugin;
use AnimeDb\Bundle\CatalogBundle\Plugin\PluginInterface;
use AnimeDb\Bundle\CatalogBundle\Entity\Item as ItemEntity;

/**
 * Plugin refiller
 *
 * @package AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Refiller extends Plugin implements PluginInterface
{
    /**
     * Item names for refill
     *
     * @var string
     */
    const FIELD_NAMES = 'names';

    /**
     * Item genres for refill
     *
     * @var string
     */
    const FIELD_GENRES = 'genres';

    /**
     * Item list of episodes for refill
     *
     * @var string
     */
    const FIELD_EPISODES = 'episodes';

    /**
     * Item summary for refill
     *
     * @var string
     */
    const FIELD_SUMMARY = 'summary';

    /**
     * Item date premiere for refill
     *
     * @var string
     */
    const FIELD_DATE_PREMIERE = 'date_premiere';

    /**
     * Item date end for refill
     *
     * @var string
     */
    const FIELD_DATE_END = 'date_end';

    /**
     * Item country for refill
     *
     * @var string
     */
    const FIELD_COUNTRY = 'country';

    /**
     * Item duration for refill
     *
     * @var string
     */
    const FIELD_DURATION = 'duration';

    /**
     * Item file info for refill
     *
     * @var string
     */
    const FIELD_FILE_INFO = 'file_info';

    /**
     * Item sources for refill
     *
     * @var string
     */
    const FIELD_SOURCES = 'sources';

    /**
     * Item episodes number for refill
     *
     * @var string
     */
    const FIELD_EPISODES_NUMBER = 'episodes_number';

    /**
     * Item images for refill
     *
     * @var string
     */
    const FIELD_IMAGES = 'images';

    /**
     * Item translate for refill
     *
     * @var string
     */
    const FIELD_TRANSLATE = 'translate';

    /**
     * Item studio for refill
     *
     * @var string
     */
    const FIELD_STUDIO = 'studio';

    /**
     * List field names
     *
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
        self::FIELD_TRANSLATE
    ];

    /**
     * @param ItemEntity $item
     * @param string $field
     *
     * @return bool
     */
    abstract public function isCanRefill(ItemEntity $item, $field);

    /**
     * Refill item field
     *
     * @param ItemEntity $item
     * @param string $field
     *
     * @return ItemEntity
     */
    abstract public function refill(ItemEntity $item, $field);

    /**
     * @param ItemEntity $item
     * @param string $field
     *
     * @return bool
     */
    abstract public function isCanSearch(ItemEntity $item, $field);

    /**
     * @param ItemEntity $item
     * @param string $field
     *
     * @return Item[]
     */
    abstract public function search(ItemEntity $item, $field);

    /**
     * Refill item field from search result
     *
     * @param ItemEntity $item
     * @param string $field
     * @param array $data
     *
     * @return ItemEntity
     */
    abstract public function refillFromSearchResult(ItemEntity $item, $field, array $data);
}
