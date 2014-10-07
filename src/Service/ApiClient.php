<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Service;

use Guzzle\Http\Client;

/**
 * API client
 *
 * @package AnimeDb\Bundle\CatalogBundle\Service
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ApiClient
{
    /**
     * API server host
     *
     * @var string
     */
    const API_HOST = 'http://anime-db.org';

    /**
     * API request prefix
     *
     * @var string
     */
    const API_PREFIX = '/api';

    /**
     * API version
     *
     * @var string
     */
    const API_VERSION = 1;

    /**
     * API default locale
     *
     * @var string
     */
    const API_DEFAULT_LOCALE = 'en';

    /**
     * List of available locales
     *
     * @var array
     */
    protected $locales = ['ru', 'en'];

    /**
     * Used locale
     *
     * @var array
     */
    protected $locale = self::API_DEFAULT_LOCALE;

    /**
     * Client
     *
     * @var \Guzzle\Http\Client
     */
    protected $client;

    /**
     * Construct
     *
     * @param string $locale
     */
    public function __construct($locale)
    {
        $locale = substr($locale, 0, 2);
        if (in_array($locale, $this->locales)) {
            $this->locale = $locale;
        }
    }

    /**
     * Get data from API
     *
     * @param string $request
     *
     * @return \Guzzle\Http\Message\Response
     */
    public function get($request)
    {
        return $this->getClient()
            ->get(self::API_PREFIX.'/v'.self::API_VERSION.'/'.$this->locale.'/'.$request)
            ->send();
    }

    /**
     * Get client
     *
     * @return \Guzzle\Http\Client
     */
    protected function getClient()
    {
        if (!($this->client instanceof Client)) {
            $this->client = new Client(self::API_HOST);
        }
        return $this->client;
    }
}
