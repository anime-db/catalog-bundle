<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use AnimeDb\Bundle\CatalogBundle\Plugin\Chain;
use AnimeDb\Bundle\CatalogBundle\Entity\Item;

/**
 * Menu builder
 *
 * @package AnimeDb\Bundle\CatalogBundle\Menu
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Builder extends ContainerAware
{

    /**
     * Link to documentation by update the application on Windows XP
     * 
     * @var string
     */
    const DOC_LINK = 'http://anime-db.org/%locale%/guide/';

    /**
     * Default documentation locale
     * 
     * @var string
     */
    const DEFAULT_DOC_LOCALE = 'en';

    /**
     * Supported documentation locale
     *
     * @var array
     */
    protected $support_locales = ['en', 'ru'];

    /**
     * Builder main menu
     * 
     * @param \Knp\Menu\FactoryInterface $factory
     * @param array $options
     *
     * @return 
     */
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        /* @var $menu \Knp\Menu\ItemInterface */
        $menu = $factory->createItem('root');

        $menu->addChild('Search', ['route' => 'home_search'])
            ->setLinkAttribute('class', 'icon-label icon-gray-search');
        $add = $menu->addChild('Add record')
            ->setLabelAttribute('class', 'icon-label icon-gray-add');

        // synchronization items
        /* @var \AnimeDb\Bundle\CatalogBundle\Plugin\Import\Chain */
        $import = $this->container->get('anime_db.plugin.import');
        /* @var \AnimeDb\Bundle\CatalogBundle\Plugin\Export\Chain */
        $export = $this->container->get('anime_db.plugin.export');
        if ($import->getPlugins() || $export->getPlugins()) {
            $sync = $menu->addChild('Synchronization');
            // add import plugin items
            $this->addPluginItems($import, $sync, 'Import items');
            // add export plugin items
            $this->addPluginItems($export, $sync, 'Export items');
        }

        $settings = $menu->addChild('Settings')
            ->setLabelAttribute('class', 'icon-label icon-gray-settings');

        // add search plugin items
        $chain = $this->container->get('anime_db.plugin.search_fill');
        $this->addPluginItems(
            $chain,
            $add,
            'Search by name',
            'Search by name the source of filling item'
        );
        if ($chain->getPlugins()) {
            $add->addChild('Search in all plugins', ['route' => 'fill_search_in_all'])
                ->setAttribute('title', $this->container->get('translator')->trans('Search by name in all plugins'))
                ->setLinkAttribute('class', 'icon-label icon-white-search');
        }
        // add filler plugin items
        $this->addPluginItems(
            $this->container->get('anime_db.plugin.filler'),
            $add,
            'Fill from source',
            'Fill record from source (example source is URL)'
        );
        // add manually
        $add->addChild('Fill manually', ['route' => 'item_add_manually'])
            ->setLinkAttribute('class', 'icon-label icon-white-add');

        $settings->addChild('File storages', ['route' => 'storage_list'])
            ->setLinkAttribute('class', 'icon-label icon-white-storage');
        $settings->addChild('List of notice', ['route' => 'notice_list'])
            ->setLinkAttribute('class', 'icon-label icon-white-alert');
        $settings->addChild('Labels', ['route' => 'home_labels'])
            ->setLinkAttribute('class', 'icon-label icon-white-label');
        $plugins = $settings->addChild('Plugins')
            ->setLabelAttribute('class', 'icon-label icon-white-plugin');
        $settings->addChild('Update', ['route' => 'update'])
            ->setLinkAttribute('class', 'icon-label icon-white-update');
        $settings->addChild('General', ['route' => 'home_settings'])
            ->setLinkAttribute('class', 'icon-label icon-white-settings');

        // plugins
        $plugins->addChild('Installed', ['route' => 'plugin_installed'])
            ->setLinkAttribute('class', 'icon-label icon-white-plugin');
        $plugins->addChild('Store', ['route' => 'plugin_store'])
            ->setLinkAttribute('class', 'icon-label icon-white-shop');
        // add settings plugin items
        foreach ($this->container->get('anime_db.plugin.setting')->getPlugins() as $plugin) {
            $plugin->buildMenu($plugins);
        }

        // add link to guide
        $locale = substr($this->container->get('request')->getLocale(), 0, 2);
        $locale = in_array($locale, $this->support_locales) ? $locale : self::DEFAULT_DOC_LOCALE;
        $settings->addChild('Help', ['uri' => str_replace('%locale%', $locale, self::DOC_LINK)])
            ->setLinkAttribute('class', 'icon-label icon-white-help');

        return $menu;
    }

    /**
     * Add plugin items in menu
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Service\Plugin\Chain $chain
     * @param \Knp\Menu\ItemInterface $root
     * @param string $label
     * @param string|null $title
     */
    private function addPluginItems(Chain $chain, ItemInterface $root, $label, $title = '')
    {
        if ($chain->getPlugins()) {
            $group = $root->addChild($label);
            if ($title) {
                $group->setAttribute('title', $this->container->get('translator')->trans($title));
            }
        }

        // add child items
        foreach ($chain->getPlugins() as $plugin) {
            $plugin->buildMenu($group);
        }
    }

    /**
     * Builder main menu
     * 
     * @param \Knp\Menu\FactoryInterface $factory
     * @param array $item
     *
     * @return 
     */
    public function itemMenu(FactoryInterface $factory, array $options)
    {
        if (empty($options['item']) || !($options['item'] instanceof Item)) {
            throw new \InvalidArgumentException('Item is not found');
        }
        /* @var $menu \Knp\Menu\ItemInterface */
        $menu = $factory->createItem('root');
        $params = ['id' => $options['item']->getId(), 'name' => $options['item']->getUrlName()];

        $menu->addChild('Change record', ['route' => 'item_change', 'routeParameters' => $params])
            ->setLinkAttribute('class', 'icon-label icon-edit');

        // add settings plugin items
        $chain = $this->container->get('anime_db.plugin.item');
        /* @var $plugin \AnimeDb\Bundle\CatalogBundle\Plugin\Item\Item */
        foreach ($chain->getPlugins() as $plugin) {
            $plugin->buildMenu($menu, $options['item']);
        }

        $menu->addChild('Delete record', ['route' => 'item_delete', 'routeParameters' => $params])
            ->setLinkAttribute('class', 'icon-label icon-delete')
            ->setLinkAttribute('data-message', $this->container->get('translator')->trans(
                'Are you sure want to delete %name%?',
                ['%name%' => $options['item']->getName()]
            ));

        return $menu;
    }
}