<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin;

/**
 * Chain plugins
 * 
 * @package AnimeDb\Bundle\CatalogBundle\Plugin
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Chain
{
    /**
     * @var array
     */
    protected $plugins = [];

    /**
     * @var array
     */
    protected $titles = [];

    /**
     * @param Plugin $plugin
     */
    public function addPlugin(Plugin $plugin) {
        $this->plugins[$plugin->getName()] = $plugin;
        $this->titles[$plugin->getName()] = $plugin->getTitle();
        ksort($this->plugins);
        ksort($this->titles);
    }

    /**
     * @param string $name
     *
     * @return Plugin|null
     */
    public function getPlugin($name) {
        if (array_key_exists($name, $this->plugins)) {
            return $this->plugins[$name];
        }
        return null;
    }

    /**
     * @return bool
     */
    public function hasPlugins()
    {
        return !empty($this->plugins);
    }

    /**
     * @return Plugin[]
     */
    public function getPlugins() {
        return $this->plugins;
    }

    /**
     * @return array
     */
    public function getNames() {
        return array_keys($this->plugins);
    }

    /**
     * @return array
     */
    public function getTitles() {
        return $this->titles;
    }
}
