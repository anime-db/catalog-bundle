<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller;

use AnimeDb\Bundle\CatalogBundle\Plugin\PluginInterface;
use AnimeDb\Bundle\CatalogBundle\Entity\Item as ItemEntity;

interface RefillerInterface extends PluginInterface
{
    /**
     * Item names for refill.
     *
     * @var string
     */
    const FIELD_NAMES = 'names';

    /**
     * Item genres for refill.
     *
     * @var string
     */
    const FIELD_GENRES = 'genres';

    /**
     * Item list of episodes for refill.
     *
     * @var string
     */
    const FIELD_EPISODES = 'episodes';

    /**
     * Item summary for refill.
     *
     * @var string
     */
    const FIELD_SUMMARY = 'summary';

    /**
     * Item date premiere for refill.
     *
     * @var string
     */
    const FIELD_DATE_PREMIERE = 'date_premiere';

    /**
     * Item date end for refill.
     *
     * @var string
     */
    const FIELD_DATE_END = 'date_end';

    /**
     * Item country for refill.
     *
     * @var string
     */
    const FIELD_COUNTRY = 'country';

    /**
     * Item duration for refill.
     *
     * @var string
     */
    const FIELD_DURATION = 'duration';

    /**
     * Item file info for refill.
     *
     * @var string
     */
    const FIELD_FILE_INFO = 'file_info';

    /**
     * Item sources for refill.
     *
     * @var string
     */
    const FIELD_SOURCES = 'sources';

    /**
     * Item episodes number for refill.
     *
     * @var string
     */
    const FIELD_EPISODES_NUMBER = 'episodes_number';

    /**
     * Item images for refill.
     *
     * @var string
     */
    const FIELD_IMAGES = 'images';

    /**
     * Item translate for refill.
     *
     * @var string
     */
    const FIELD_TRANSLATE = 'translate';

    /**
     * Item studio for refill.
     *
     * @var string
     */
    const FIELD_STUDIO = 'studio';

    /**
     * @param ItemEntity $item
     * @param string $field
     *
     * @return bool
     */
    public function isCanRefill(ItemEntity $item, $field);

    /**
     * Refill item field.
     *
     * @param ItemEntity $item
     * @param string $field
     *
     * @return ItemEntity
     */
    public function refill(ItemEntity $item, $field);

    /**
     * @param ItemEntity $item
     * @param string $field
     *
     * @return bool
     */
    public function isCanSearch(ItemEntity $item, $field);

    /**
     * @param ItemEntity $item
     * @param string $field
     *
     * @return Item[]
     */
    public function search(ItemEntity $item, $field);

    /**
     * Refill item field from search result.
     *
     * @param ItemEntity $item
     * @param string $field
     * @param array $data
     *
     * @return ItemEntity
     */
    public function refillFromSearchResult(ItemEntity $item, $field, array $data);
}
