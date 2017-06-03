<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use AnimeDb\Bundle\ApiClientBundle\Service\Client;
use AnimeDb\Bundle\CatalogBundle\Plugin\Chain;
use AnimeDb\Bundle\CatalogBundle\Entity\Item;
use AnimeDb\Bundle\CatalogBundle\Plugin\PluginInMenuInterface;
use AnimeDb\Bundle\CatalogBundle\Plugin\Import\Chain as ChainImport;
use AnimeDb\Bundle\CatalogBundle\Plugin\Export\Chain as ChainExport;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Chain as ChainFiller;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Chain as ChainSearch;
use AnimeDb\Bundle\CatalogBundle\Plugin\Setting\Chain as ChainSetting;
use AnimeDb\Bundle\CatalogBundle\Plugin\Item\Chain as ChainItem;
use AnimeDb\Bundle\CatalogBundle\Plugin\Item\ItemInterface as ItemPluginInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Menu builder.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Builder
{
    /**
     * Link to guide by update the application on Windows XP.
     *
     * @var string
     */
    const GUIDE_LINK = '/guide/';

    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @var RequestStack
     */
    protected $request_stack;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var Client
     */
    protected $api_client;

    /**
     * @var ChainImport
     */
    protected $plugin_import;

    /**
     * @var ChainExport
     */
    protected $plugin_export;

    /**
     * @var ChainSearch
     */
    protected $plugin_search;

    /**
     * @var ChainFiller
     */
    protected $plugin_filler;

    /**
     * @var ChainSetting
     */
    protected $plugin_setting;

    /**
     * @var ChainItem
     */
    protected $plugin_item;

    /**
     * @param FactoryInterface $factory
     * @param RequestStack $request_stack
     * @param TranslatorInterface $translator
     * @param Client $api_client
     * @param ChainImport $plugin_import
     * @param ChainExport $plugin_export
     * @param ChainSearch $plugin_search
     * @param ChainFiller $plugin_filler
     * @param ChainSetting $plugin_setting
     * @param ChainItem $plugin_item
     */
    public function __construct(
        FactoryInterface $factory,
        RequestStack $request_stack,
        TranslatorInterface $translator,
        Client $api_client,
        ChainImport $plugin_import,
        ChainExport $plugin_export,
        ChainSearch $plugin_search,
        ChainFiller $plugin_filler,
        ChainSetting $plugin_setting,
        ChainItem $plugin_item
    ) {
        $this->factory = $factory;
        $this->request_stack = $request_stack;
        $this->translator = $translator;
        $this->api_client = $api_client;
        $this->plugin_import = $plugin_import;
        $this->plugin_export = $plugin_export;
        $this->plugin_search = $plugin_search;
        $this->plugin_filler = $plugin_filler;
        $this->plugin_setting = $plugin_setting;
        $this->plugin_item = $plugin_item;
    }

    /**
     * @return ItemInterface
     */
    public function createMainMenu()
    {
        /* @var $menu ItemInterface */
        $menu = $this->factory
            ->createItem('root')
            ->setUri($this->request_stack->getMasterRequest()->getRequestUri());

        $menu
            ->addChild('Search', ['route' => 'home_search'])
            ->setLinkAttribute('class', 'icon-label icon-gray-search');
        $add = $menu
            ->addChild('Add record')
            ->setLabelAttribute('class', 'icon-label icon-gray-add');

        // synchronization items
        if ($this->plugin_import->hasPlugins() || $this->plugin_export->hasPlugins()) {
            $sync = $menu
                ->addChild('Synchronization')
                ->setLabelAttribute('class', 'icon-label icon-white-cloud-sync');

            // add import plugin items
            $this->addPluginItems(
                $this->plugin_import,
                $sync,
                'Import items',
                '',
                'icon-label icon-white-cloud-download'
            );
            // add export plugin items
            $this->addPluginItems(
                $this->plugin_export,
                $sync,
                'Export items',
                '',
                'icon-label icon-white-cloud-arrow-up'
            );
        }

        $settings = $menu
            ->addChild('Settings')
            ->setLabelAttribute('class', 'icon-label icon-gray-settings');

        // add search plugin items
        $this->addPluginItems(
            $this->plugin_search,
            $add,
            'Search by name',
            'Search by name the source of filling item',
            'icon-label icon-white-search'
        );
        if ($this->plugin_search->hasPlugins()) {
            $add
                ->addChild('Search in all plugins', ['route' => 'fill_search_in_all'])
                ->setAttribute('title', $this->translator->trans('Search by name in all plugins'))
                ->setLinkAttribute('class', 'icon-label icon-white-cloud-search');
            $add
                ->addChild('Add from URL', ['route' => 'fill_search_filler'])
                ->setAttribute('title', $this->translator->trans('Search plugin by the URL for filling item'))
                ->setLinkAttribute('class', 'icon-label icon-white-cloud-search');
        }
        // add filler plugin items
        $this->addPluginItems(
            $this->plugin_filler,
            $add,
            'Fill from source',
            'Fill record from source (example source is URL)',
            'icon-label icon-white-share'
        );
        // add manually
        $add
            ->addChild('Fill manually', ['route' => 'item_add_manually'])
            ->setLinkAttribute('class', 'icon-label icon-white-add');

        $settings
            ->addChild('File storages', ['route' => 'storage_list'])
            ->setLinkAttribute('class', 'icon-label icon-white-storage');
        $settings
            ->addChild('List of notice', ['route' => 'notice_list'])
            ->setLinkAttribute('class', 'icon-label icon-white-alert');
        $settings
            ->addChild('Labels', ['route' => 'label'])
            ->setLinkAttribute('class', 'icon-label icon-white-label');
        $plugins = $settings
            ->addChild('Plugins')
            ->setLabelAttribute('class', 'icon-label icon-white-plugin');
        $settings
            ->addChild('Update', ['route' => 'update'])
            ->setLinkAttribute('class', 'icon-label icon-white-update');
        $settings
            ->addChild('General', ['route' => 'home_settings'])
            ->setLinkAttribute('class', 'icon-label icon-white-settings');

        // plugins
        $plugins
            ->addChild('Installed', ['route' => 'plugin_installed'])
            ->setLinkAttribute('class', 'icon-label icon-white-plugin');
        $plugins
            ->addChild('Store', ['route' => 'plugin_store'])
            ->setLinkAttribute('class', 'icon-label icon-white-shop');
        // add settings plugin items
        foreach ($this->plugin_setting->getPlugins() as $plugin) {
            /* @var $plugin PluginInMenuInterface */
            $plugin->buildMenu($plugins);
        }

        // add link to guide
        $settings
            ->addChild('Help', ['uri' => $this->api_client->getSiteUrl(self::GUIDE_LINK)])
            ->setLinkAttribute('class', 'icon-label icon-white-help');

        return $menu;
    }

    /**
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
                $group->setAttribute('title', $this->translator->trans($title));
            }
            if ($class) {
                $group->setLabelAttribute('class', $class);
            }

            // add child items
            foreach ($chain->getPlugins() as $plugin) {
                if ($plugin instanceof PluginInMenuInterface) {
                    $plugin->buildMenu($group);
                }
            }
        }
    }

    /**
     * @param array $options
     *
     * @return ItemInterface
     */
    public function createItemMenu(array $options)
    {
        if (empty($options['item']) || !($options['item'] instanceof Item)) {
            throw new \InvalidArgumentException('Item is not found');
        }

        /* @var $item Item */
        $item = $options['item'];

        /* @var $menu ItemInterface */
        $menu = $this->factory
            ->createItem('root')
            ->setUri($this->request_stack->getMasterRequest()->getRequestUri());
        $params = [
            'id' => $item->getId(),
            'name' => $item->getUrlName(),
        ];

        $menu
            ->addChild('Change record', ['route' => 'item_change', 'routeParameters' => $params])
            ->setLinkAttribute('class', 'icon-label icon-edit');

        // add settings plugin items
        /* @var $plugin ItemPluginInterface */
        foreach ($this->plugin_item->getPlugins() as $plugin) {
            $plugin->buildMenu($menu, $options['item']);
        }

        $menu->addChild('Delete record', ['route' => 'item_delete', 'routeParameters' => $params])
            ->setLinkAttribute('class', 'icon-label icon-delete')
            ->setLinkAttribute('data-message', $this->translator->trans(
                'Are you sure want to delete %name%?',
                ['%name%' => $item->getName()]
            ));

        return $menu;
    }
}
