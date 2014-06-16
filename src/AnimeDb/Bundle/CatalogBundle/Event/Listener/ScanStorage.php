<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Event\Listener;

use AnimeDb\Bundle\CatalogBundle\Event\Storage\DeleteItemFiles;
use AnimeDb\Bundle\CatalogBundle\Event\Storage\DetectedNewFiles;
use AnimeDb\Bundle\CatalogBundle\Event\Storage\UpdateItemFiles;
use AnimeDb\Bundle\CatalogBundle\Event\Storage\AddNewItem;
use AnimeDb\Bundle\CatalogBundle\Event\Storage\StoreEvents;
use AnimeDb\Bundle\AppBundle\Entity\Notice;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\TwigBundle\TwigEngine;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Chain as SearchChain;
use AnimeDb\Bundle\CatalogBundle\Form\Plugin\Search as SearchPluginForm;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormFactory;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Search;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Filler;
use AnimeDb\Bundle\CatalogBundle\Entity\Item;

/**
 * Storages scan listener
 *
 * @package AnimeDb\Bundle\CatalogBundle\Event\Listener
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ScanStorage
{
    /**
     * Entity manager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * Templating
     *
     * @var \Symfony\Bundle\TwigBundle\TwigEngine
     */
    protected $templating;

    /**
     * Search chain
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Chain
     */
    protected $search;

    /**
     * Router
     *
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    protected $router;

    /**
     * Form factory
     *
     * @var \Symfony\Component\Form\FormFactory
     */
    protected $form_factory;

    /**
     * Notice type: files for item is not found
     *
     * @var string
     */
    const NOTICE_TYPE_ITEM_FILES_NOT_FOUND = 'item_files_not_found';

    /**
     * Notice type: Detected files for new item
     *
     * @var string
     */
    const NOTICE_TYPE_DETECTED_FILES_FOR_NEW_ITEM = 'detected_files_for_new_item';

    /**
     * Notice type: Changes are detected in files of item
     *
     * @var string
     */
    const NOTICE_TYPE_UPDATE_ITEM_FILES = 'update_item_files';

    /**
     * Notice type: Detected and added new item
     *
     * @var string
     */
    const NOTICE_TYPE_ADDED_NEW_ITEM = 'added_new_item';

    /**
     * Construct
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Symfony\Bundle\TwigBundle\TwigEngine $templating
     * @param \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Chain $search
     * @param \Symfony\Bundle\FrameworkBundle\Routing\Router $router
     * @param \Symfony\Component\Form\FormFactory $form_factory
     */
    public function __construct(
        EntityManager $em,
        TwigEngine $templating,
        SearchChain $search,
        Router $router,
        FormFactory $form_factory
    ) {
        $this->em = $em;
        $this->templating = $templating;
        $this->search = $search;
        $this->router = $router;
        $this->form_factory = $form_factory;
    }

    /**
     * On delete item files
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Event\Storage\DeleteItemFiles $event
     */
    public function onDeleteItemFiles(DeleteItemFiles $event)
    {
        $notice = new Notice();
        $notice->setType(self::NOTICE_TYPE_ITEM_FILES_NOT_FOUND);
        $notice->setMessage($this->templating->render(
            'AnimeDbCatalogBundle:Notice:messages/delete_item_files.html.twig',
            ['item' => $event->getItem()]
        ));
        $this->em->persist($notice);
    }

    /**
     * On detected new files send notice
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Event\Storage\DetectedNewFiles $event
     */
    public function onDetectedNewFilesSendNotice(DetectedNewFiles $event)
    {
        if (!$event->isPropagationStopped()) {
            $notice = new Notice();
            // get link for search item
            $link = null;
            if ($plugin = $this->search->getDafeultPlugin()) {
                $link = $plugin->getLinkForSearch($event->getName());
            } elseif ($this->search->getPlugins()) {
                $link = $this->router->generate(
                    'fill_search_in_all',
                    [SearchPluginForm::FORM_NAME => ['name' => $event->getName()]]
                );
            }

            $notice->setType(self::NOTICE_TYPE_DETECTED_FILES_FOR_NEW_ITEM);
            $notice->setMessage($this->templating->render(
                'AnimeDbCatalogBundle:Notice:messages/detected_new_files.html.twig',
                ['storage' => $event->getStorage(), 'name' => $event->getName(), 'link' => $link]
            ));
            $this->em->persist($notice);
        }
    }

    /**
     * On update item files
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Event\Storage\UpdateItemFiles $event
     */
    public function onUpdateItemFiles(UpdateItemFiles $event)
    {
        $notice = new Notice();
        $notice->setType(self::NOTICE_TYPE_UPDATE_ITEM_FILES);
        $notice->setMessage($this->templating->render(
            'AnimeDbCatalogBundle:Notice:messages/update_item_files.html.twig',
            ['item' => $event->getItem()]
        ));
        $this->em->persist($notice);
    }

    /**
     * On detected new files try add it
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Event\Storage\DetectedNewFiles $event
     */
    public function onDetectedNewFilesTryAdd(DetectedNewFiles $event)
    {
        // search from dafeult plugin
        $dafeult_plugin = null;
        if (($dafeult_plugin = $this->search->getDafeultPlugin()) &&
            $dafeult_plugin->getFiller() instanceof Filler &&
            $this->tryAddItem($dafeult_plugin, $dafeult_plugin->getFiller(), $event)
        ) {
            return true;
        }

        // search from all plugins
        foreach ($this->search->getPlugins() as $plugin) {
            if ((!($dafeult_plugin instanceof Search) || $dafeult_plugin !== $plugin) &&
                $plugin->getFiller() instanceof Filler &&
                $this->tryAddItem($plugin, $plugin->getFiller(), $event)
            ) {
                return true;
            }
        }
    }

    /**
     * Try to add item
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Search $search
     * @param \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Filler $filler
     * @param \AnimeDb\Bundle\CatalogBundle\Event\Storage\DetectedNewFiles $event
     *
     * @return boolean
     */
    protected function tryAddItem(Search $search, Filler $filler, DetectedNewFiles $event)
    {
        $list = [];
        // try search a new item
        try {
            $list = $search->search(['name' => $event->getName()]);
        } catch (\Exception $e) {}

        // fill from search result
        if (count($list) == 1) {
            $item = null;
            try {
                /* @var $item \AnimeDb\Bundle\CatalogBundle\Entity\Item */
                $item = $filler->fillFromSearchResult(array_pop($list));
            } catch (\Exception $e) {}

            if ($item instanceof Item) {
                // save new item
                $item->setStorage($event->getStorage());
                $item->setPath(
                    $event->getFile()->getPathname().
                    ($event->getFile()->isDir() ? DIRECTORY_SEPARATOR : '')
                );

                // stop current event and dispatch new event of added item
                $event->stopPropagation();
                $event->getDispatcher()->dispatch(StoreEvents::ADD_NEW_ITEM, new AddNewItem($item, $filler));
                return true;
            }
        }
        return false;
    }

    /**
     * On added new item send notice
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Event\Storage\AddNewItem $event
     */
    public function onAddNewItemSendNotice(AddNewItem $event)
    {
        $notice = new Notice();
        $notice->setType(self::NOTICE_TYPE_ADDED_NEW_ITEM);
        $notice->setMessage($this->templating->render(
            'AnimeDbCatalogBundle:Notice:messages/added_new_item.html.twig',
            ['storage' => $event->getItem()->getStorage(), 'item' => $event->getItem()]
        ));
        $this->em->persist($notice);
    }

    /**
     * On added new item persist it
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Event\Storage\AddNewItem $event
     */
    public function onAddNewItemPersistIt(AddNewItem $event)
    {
        $this->em->persist($event->getItem());
        $this->em->flush();
    }
}