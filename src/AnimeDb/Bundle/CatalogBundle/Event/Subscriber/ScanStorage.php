<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Event\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use AnimeDb\Bundle\CatalogBundle\Event\Storage\StoreEvents;
use AnimeDb\Bundle\CatalogBundle\Event\Storage\DeleteItemFiles;
use AnimeDb\Bundle\CatalogBundle\Event\Storage\DetectedNewFiles;
use AnimeDb\Bundle\CatalogBundle\Event\Storage\UpdateItemFiles;
use AnimeDb\Bundle\AppBundle\Entity\Notice;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\TwigBundle\TwigEngine;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Chain as SearchChain;
use AnimeDb\Bundle\CatalogBundle\Form\Plugin\Search as SearchPluginForm;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormFactory;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Filler;

/**
 * Storages scan subscriber
 *
 * @package AnimeDb\Bundle\CatalogBundle\Event\Subscriber
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ScanStorage implements EventSubscriberInterface
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
     * (non-PHPdoc)
     * @see Symfony\Component\EventDispatcher.EventSubscriberInterface::getSubscribedEvents()
     */
    public static function getSubscribedEvents()
    {
        return [
            StoreEvents::DELETE_ITEM_FILES => 'onDeleteItemFiles',
            StoreEvents::DETECTED_NEW_FILES => 'onDetectedNewFiles',
            StoreEvents::UPDATE_ITEM_FILES => 'onUpdateItemFiles',
        ];
    }

    /**
     * On delete item files
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Event\Storage\DeleteItemFiles $event
     */
    public function onDeleteItemFiles(DeleteItemFiles $event)
    {
        $notice = new Notice();
        $notice->setMessage($this->templating->render(
            'AnimeDbCatalogBundle:Notice:messages/delete_item_files.html.twig',
            ['item' => $event->getItem()]
        ));
        $this->em->persist($notice);
    }

    /**
     * On detected new files
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Event\Storage\DetectedNewFiles $event
     */
    public function onDetectedNewFiles(DetectedNewFiles $event)
    {
        if ($event->getFile()->isDir()) {
            $name = $event->getFile()->getFilename();
        } else {
            $name = pathinfo($event->getFile()->getFilename(), PATHINFO_BASENAME);
        }
        $name = trim(preg_replace('/^([^\[\]\(\)]+).*$/', '$1', $name));

        // default notice message
        $message = [
            'AnimeDbCatalogBundle:Notice:messages/detected_new_files.html.twig',
            ['storage' => $event->getStorage(), 'name' => $name, 'link' => null]
        ];

        // search item by name from default plugin
        /* @var $plugin \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Search */
        if ($plugin = $this->search->getDafeultPlugin()) {
            // link for search item
            $message[1]['link'] = $plugin->getLinkForSearch($name);

            if ($plugin->getFiller() instanceof Filler) {
                $list = [];
                // try search a new item
                try {
                    $list = $plugin->search(['name' => $name]);
                } catch (\Exception $e) {}

                // fill from search result
                if (count($list) == 1) {
                    try {
                        /* @var $item \AnimeDb\Bundle\CatalogBundle\Entity\Item */
                        $item = $plugin->getFiller()->fillFromSearchResult($list[0]);
                    } catch (\Exception $e) {}

                    if ($item) {
                        // save new item
                        $item->setStorage($event->getStorage());
                        $item->setPath($event->getFile()->getPathname());
                        $this->em->persist($item);
                        $this->em->flush();

                        // change notice message
                        $message = [
                            'AnimeDbCatalogBundle:Notice:messages/added_new_item.html.twig',
                            ['storage' => $event->getStorage(), 'item' => $item]
                        ];
                    }
                }
            }
        }
        $notice = new Notice();
        $notice->setMessage($this->templating->render($message[0], $message[1]));

        $this->em->persist($notice);
    }

    /**
     * On update item files
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Event\Storage\UpdateItemFiles $event
     */
    public function onUpdateItemFiles(UpdateItemFiles $event)
    {
        $notice = new Notice();
        $notice->setMessage($this->templating->render(
            'AnimeDbCatalogBundle:Notice:messages/update_item_files.html.twig',
            ['item' => $event->getItem()]
        ));
        $this->em->persist($notice);
    }
}