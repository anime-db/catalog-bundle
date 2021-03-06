<?php
/**
 * AnimeDb package.
 *
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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\TwigBundle\TwigEngine;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Chain as SearchChain;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Search as SearchPluginForm;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormFactory;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\SearchInterface;
use AnimeDb\Bundle\CatalogBundle\Entity\Item;

/**
 * Storage scan listener.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ScanStorage
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var TwigEngine
     */
    protected $templating;

    /**
     * @var SearchChain
     */
    protected $search;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var FormFactory
     */
    protected $form_factory;

    /**
     * Notice type: files for item is not found.
     *
     * @var string
     */
    const NOTICE_TYPE_ITEM_FILES_NOT_FOUND = 'item_files_not_found';

    /**
     * Notice type: Detected files for new item.
     *
     * @var string
     */
    const NOTICE_TYPE_DETECTED_FILES_FOR_NEW_ITEM = 'detected_files_for_new_item';

    /**
     * Notice type: Changes are detected in files of item.
     *
     * @var string
     */
    const NOTICE_TYPE_UPDATE_ITEM_FILES = 'update_item_files';

    /**
     * Notice type: Detected and added new item.
     *
     * @var string
     */
    const NOTICE_TYPE_ADDED_NEW_ITEM = 'added_new_item';

    /**
     * Construct.
     *
     * @param EntityManagerInterface $em
     * @param TwigEngine $templating
     * @param SearchChain $search
     * @param Router $router
     * @param FormFactory $form_factory
     */
    public function __construct(
        EntityManagerInterface $em,
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
     * @param DeleteItemFiles $event
     */
    public function onDeleteItemFiles(DeleteItemFiles $event)
    {
        $this->sendNotice(self::NOTICE_TYPE_ITEM_FILES_NOT_FOUND, ['item' => $event->getItem()]);
    }

    /**
     * @param DetectedNewFiles $event
     */
    public function onDetectedNewFilesSendNotice(DetectedNewFiles $event)
    {
        // get link for search item
        $link = null;
        if ($plugin = $this->search->getDafeultPlugin()) {
            $link = $plugin->getLinkForSearch($event->getName());
        } elseif ($this->search->hasPlugins()) {
            $link = $this->router->generate(
                'fill_search_in_all',
                [SearchPluginForm::FORM_NAME => ['name' => $event->getName()]]
            );
        }

        $this->sendNotice(
            self::NOTICE_TYPE_DETECTED_FILES_FOR_NEW_ITEM,
            [
                'storage' => $event->getStorage(),
                'name' => $event->getName(),
                'link' => $link,
            ]
        );
    }

    /**
     * @param UpdateItemFiles $event
     */
    public function onUpdateItemFiles(UpdateItemFiles $event)
    {
        $this->sendNotice(self::NOTICE_TYPE_UPDATE_ITEM_FILES, ['item' => $event->getItem()]);
    }

    /**
     * @param DetectedNewFiles $event
     *
     * @return bool
     */
    public function onDetectedNewFilesTryAdd(DetectedNewFiles $event)
    {
        // search from default plugin
        $default_plugin = null;
        if (($default_plugin = $this->search->getDafeultPlugin()) && $this->tryAddItem($default_plugin, $event)) {
            return true;
        }

        // search from all plugins
        foreach ($this->search->getPlugins() as $plugin) {
            /* @var $plugin SearchInterface */
            if ($plugin !== $default_plugin && $this->tryAddItem($plugin, $event)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param SearchInterface $search
     * @param DetectedNewFiles $event
     *
     * @return bool
     */
    protected function tryAddItem(SearchInterface $search, DetectedNewFiles $event)
    {
        $item = $search->getCatalogItem($event->getName());

        if ($item instanceof Item) {
            // save new item
            $item->setStorage($event->getStorage());
            $item->setPath(
                $event->getFile()->getPathname().
                ($event->getFile()->isDir() ? DIRECTORY_SEPARATOR : '')
            );

            // stop current event and dispatch new event of added item
            $event->stopPropagation();
            $event->getDispatcher()->dispatch(
                StoreEvents::ADD_NEW_ITEM,
                new AddNewItem($item, $search->getFiller())
            );

            return true;
        }

        return false;
    }

    /**
     * @param AddNewItem $event
     */
    public function onAddNewItemSendNotice(AddNewItem $event)
    {
        $this->sendNotice(
            self::NOTICE_TYPE_ADDED_NEW_ITEM,
            [
                'storage' => $event->getItem()->getStorage(),
                'item' => $event->getItem(),
            ]
        );
    }

    /**
     * @param AddNewItem $event
     */
    public function onAddNewItemPersistIt(AddNewItem $event)
    {
        $this->em->persist($event->getItem());
        $this->em->flush();
    }

    /**
     * @param string $type
     * @param array $params
     */
    protected function sendNotice($type, array $params)
    {
        $notice = new Notice();
        $notice->setType($type);
        $notice->setMessage($this->templating->render(
            'AnimeDbCatalogBundle:Notice:messages/'.$type.'.html.twig',
            $params
        ));
        $this->em->persist($notice);
    }
}
