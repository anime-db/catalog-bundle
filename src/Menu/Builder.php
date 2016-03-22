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

use AnimeDb\Bundle\CatalogBundle\Plugin\PluginInMenuInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use AnimeDb\Bundle\CatalogBundle\Plugin\Chain;
use AnimeDb\Bundle\CatalogBundle\Entity\Item;
use AnimeDb\Bundle\CatalogBundle\Plugin\Import\Chain as ChainImport;
use AnimeDb\Bundle\CatalogBundle\Plugin\Export\Chain as ChainExport;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Chain as ChainFiller;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Chain as ChainSearch;
use AnimeDb\Bundle\CatalogBundle\Plugin\Item\Item as ItemPlugin;

/**
 * Menu builder
 *
 * @package AnimeDb\Bundle\CatalogBundle\Menu
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Builder extends ContainerAware
{
    /**
     * Link to guide by update the application on Windows XP
     * 
     * @var string
     */
    const GUIDE_LINK = '/guide/';

    /**
     * @param FactoryInterface $factory
     * @param array $options
     *
     * @return ItemInterface
     */
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        /* @var $menu ItemInterface */
        $menu = $factory->createItem('root');

        $menu->addChild('Search', ['route' => 'home_search'])
            ->setLinkAttribute('class', 'icon-label icon-gray-search');
        $add = $menu->addChild('Add record')
            ->setLabelAttribute('class', 'icon-label icon-gray-add');

        // synchronization items
        /* @var $import ChainImport */
        $import = $this->container->get('anime_db.plugin.import');
        /* @var $export ChainExport */
        $export = $this->container->get('anime_db.plugin.export');
        if ($import->hasPlugins() || $export->hasPlugins()) {
            $sync = $menu->addChild('Synchronization')
                ->setLabelAttribute('class', 'icon-label icon-white-cloud-sync');
            // add import plugin items
            $this->addPluginItems($import, $sync, 'Import items', '', 'icon-label icon-white-cloud-download');
            // add export plugin items
            $this->addPluginItems($export, $sync, 'Export items', '', 'icon-label icon-white-cloud-arrow-up');
        }

        $settings = $menu->addChild('Settings')
            ->setLabelAttribute('class', 'icon-label icon-gray-settings');

        // add search plugin items
        /* @var $chain ChainSearch */
        $chain = $this->container->get('anime_db.plugin.search_fill');
        $this->addPluginItems(
            $chain,
            $add,
            'Search by name',
            'Search by name the source of filling item',
            'icon-label icon-white-search'
        );
        if ($chain->hasPlugins()) {
            $add->addChild('Search in all plugins', ['route' => 'fill_search_in_all'])
                ->setAttribute('title', $this->container->get('translator')->trans('Search by name in all plugins'))
                ->setLinkAttribute('class', 'icon-label icon-white-cloud-search');
            $add->addChild('Add from URL', ['route' => 'fill_search_filler'])
                ->setAttribute('title', $this->container->get('translator')->trans('Search plugin by the URL for filling item'))
                ->setLinkAttribute('class', 'icon-label icon-white-cloud-search');
        }
        // add filler plugin items
        /* @var $filler ChainFiller */
        $filler = $this->container->get('anime_db.plugin.filler');
        $this->addPluginItems(
            $filler,
            $add,
            'Fill from source',
            'Fill record from source (example source is URL)',
            'icon-label icon-white-share'
        );
        // add manually
        $add->addChild('Fill manually', ['route' => 'item_add_manually'])
            ->setLinkAttribute('class', 'icon-label icon-white-add');

        $settings->addChild('File storages', ['route' => 'storage_list'])
            ->setLinkAttribute('class', 'icon-label icon-white-storage');
        $settings->addChild('List of notice', ['route' => 'notice_list'])
            ->setLinkAttribute('class', 'icon-label icon-white-alert');
        $settings->addChild('Labels', ['route' => 'label'])
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
            /* @var $plugin PluginInMenuInterface */
            $plugin->buildMenu($plugins);
        }

        // add link to guide
        $settings->addChild('Help', ['uri' => $this->container->get('anime_db.api.client')->getSiteUrl(self::GUIDE_LINK)])
            ->setLinkAttribute('class', 'icon-label icon-white-help');

        return $menu;
    }

    /**
     * Add plugin items in menu
     *
     * @param Chain $chain
     * @param ItemInterface $root
     * @param string $label
     * @param string|null $title
     * @param string|null $class
     */
    private function addPluginItems(Chain $chain, ItemInterface $root, $label, $title = '', $class = '')
    {
        if ($chain->hasPlugins()) {
            $group = $root->addChild($label);
            if ($title) {
                $group->setAttribute('title', $this->container->get('translator')->trans($title));
            }
            if ($class) {
                $group->setLabelAttribute('class', $class);
            }

            // add child items
            foreach ($chain->getPlugins() as $plugin) {
                /* @var $plugin PluginInMenuInterface */
                $plugin->buildMenu($group);
            }
        }
    }

    /**
     * @param FactoryInterface $factory
     * @param array $options
     *
     * @return ItemInterface
     */
    public function itemMenu(FactoryInterface $factory, array $options)
    {
        if (empty($options['item']) || !($options['item'] instanceof Item)) {
            throw new \InvalidArgumentException('Item is not found');
        }
        /* @var $item Item */
        $item = $options['item'];

        /* @var $menu ItemInterface */
        $menu = $factory->createItem('root');
        $params = [
            'id' => $item->getId(),
            'name' => $item->getUrlName()
        ];

        $menu->addChild('Change record', ['route' => 'item_change', 'routeParameters' => $params])
            ->setLinkAttribute('class', 'icon-label icon-edit');

        // add settings plugin items
        $chain = $this->container->get('anime_db.plugin.item');
        /* @var $plugin ItemPlugin */
        foreach ($chain->getPlugins() as $plugin) {
            $plugin->buildMenu($menu, $options['item']);
        }

        $menu->addChild('Delete record', ['route' => 'item_delete', 'routeParameters' => $params])
            ->setLinkAttribute('class', 'icon-label icon-delete')
            ->setLinkAttribute('data-message', $this->container->get('translator')->trans(
                'Are you sure want to delete %name%?',
                ['%name%' => $item->getName()]
            ));

        return $menu;
    }
}
