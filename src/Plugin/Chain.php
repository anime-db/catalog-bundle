<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CatalogBundle\Plugin;

/**
 * Chain plugins.
 *
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
     * @param PluginInterface $plugin
     */
    public function addPlugin(PluginInterface $plugin)
    {
        $this->plugins[$plugin->getName()] = $plugin;
        $this->titles[$plugin->getName()] = $plugin->getTitle();
        ksort($this->plugins);
        ksort($this->titles);
    }

    /**
     * @param string $name
     *
     * @return PluginInterface|null
     */
    public function getPlugin($name)
    {
        if (array_key_exists($name, $this->plugins)) {
            return $this->plugins[$name];
        }

        return;
    }

    /**
     * @return bool
     */
    public function hasPlugins()
    {
        return !empty($this->plugins);
    }

    /**
     * @return PluginInterface[]
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * @return string[]
     */
    public function getNames()
    {
        return array_keys($this->plugins);
    }

    /**
     * @return string[]
     */
    public function getTitles()
    {
        return $this->titles;
    }
}
