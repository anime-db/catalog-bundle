<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AnimeDb\Bundle\CatalogBundle\Entity\Item;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Item
 *
 * @package AnimeDb\Bundle\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ItemController extends Controller
{
    /**
     * Name of session to store item to be added
     *
     * @var string
     */
    const NAME_ITEM_ADDED = '_item_added';

    /**
     * Widget place in content
     *
     * @var string
     */
    const WIDGET_PALCE_IN_CONTENT = 'item.in_content';

    /**
     * Widget place right
     *
     * @var string
     */
    const WIDGET_PALCE_RIGHT = 'item.right';

    /**
     * Widget place bottom
     *
     * @var string
     */
    const WIDGET_PALCE_BOTTOM = 'item.bottom';

    /**
     * Items per page
     *
     * @var integer
     */
    const ITEMS_PER_PAGE = 8;

    /**
     * Default limit
     *
     * @var integer
     */
    const DEFAULT_LIMIT = 8;

    /**
     * Limit for show all items
     *
     * @var integer
     */
    const LIMIT_ALL = 0;

    /**
     * Limit name for show all items
     *
     * @var integer
     */
    const LIMIT_ALL_NAME = 'All (%total%)';

    /**
     * Limits on the number of items per page
     *
     * @var array
     */
    public static $limits = [8, 16, 32, self::LIMIT_ALL];

    /**
     * Sort items by field
     *
     * @var array
     */
    public static $sort_by_field = [
        'name'        => [
            'title' => 'Item name',
            'name'  => 'Name'
        ],
        'date_update' => [
            'title' => 'Last updated item',
            'name'  => 'Update'
        ],
        'rating' => [
            'title' => 'Item rating',
            'name'  => 'Rating'
        ],
        'date_premiere'  => [
            'title' => 'Date premiere',
            'name'  => 'Date premiere'
        ],
        'date_end'    => [
            'title' => 'End date of issue',
            'name'  => 'Date end'
        ]
    ];

    /**
     * Show item
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Item $item, Request $request)
    {
        $response = new Response();
        // caching
        if ($last_update = $this->container->getParameter('last_update')) {
            $response->setLastModified(new \DateTime($last_update));
        }
        // use item update date
        if ($response->getLastModified() < $item->getDateUpdate()) {
            $response->setLastModified($item->getDateUpdate());
        }
        // use storage update date
        if (
            $item->getStorage() instanceof Storage &&
            $response->getLastModified() < $item->getStorage()->getDateUpdate()
        ) {
            $response->setLastModified($item->getStorage()->getDateUpdate());
        }
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        return $this->render('AnimeDbCatalogBundle:Item:show.html.twig', [
            'item' => $item,
            'widget_bottom' => self::WIDGET_PALCE_BOTTOM,
            'widget_in_content' => self::WIDGET_PALCE_IN_CONTENT,
            'widget_right' => self::WIDGET_PALCE_RIGHT
        ], $response);
    }

    /**
     * Addition form
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addManuallyAction(Request $request)
    {
        $item = new Item();

        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm('anime_db_catalog_entity_item', $item);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                /* @var $repository \AnimeDb\Bundle\CatalogBundle\Repository\Item */
                $repository = $this->getDoctrine()->getRepository('AnimeDbCatalogBundle:Item');

                // Add a new entry only if no duplicates
                $duplicate = $repository->findDuplicate($item);
                if ($duplicate) {
                    $request->getSession()->set(self::NAME_ITEM_ADDED, $item);
                    return $this->redirect($this->generateUrl('item_duplicate'));
                } else {
                    return $this->addItem($item);
                }
            }
        }

        return $this->render('AnimeDbCatalogBundle:Item:add-manually.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Change item
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changeAction(Item $item, Request $request)
    {
        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm('anime_db_catalog_entity_item', $item);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($item);
                $em->flush();
                return $this->redirect($this->generateUrl(
                    'item_show',
                    ['id' => $item->getId(), 'name' => $item->getName()]
                ));
            }
        }

        return $this->render('AnimeDbCatalogBundle:Item:change.html.twig', [
            'item' => $item,
            'form' => $form->createView()
        ]);
    }

    /**
     * Delete item
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Item $item)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($item);
        $em->flush();
        return $this->redirect($this->generateUrl('home'));
    }

    /**
     * Import items
     *
     * @param string $plugin
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function importAction($plugin, Request $request)
    {
        /* @var $chain \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Chain */
        $chain = $this->get('anime_db.plugin.import');
        if (!($import = $chain->getPlugin($plugin))) {
            throw $this->createNotFoundException('Plugin \''.$plugin.'\' is not found');
        }

        $form = $this->createForm($import->getForm());

        $list = [];
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                // import items
                $list = (array)$import->import($form->getData());

                // persist entity
                $em = $this->getDoctrine()->getManager();
                foreach ($list as $key => $item) {
                    if ($item instanceof Item) {
                        $em->persist($item);
                    } else {
                        unset($list[$key]);
                    }
                }
            }
        }

        return $this->render('AnimeDbCatalogBundle:Item:import.html.twig', [
            'plugin' => $plugin,
            'items'  => $list,
            'form'   => $form->createView()
        ]);
    }

    /**
     * Confirm duplicate item
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function duplicateAction(Request $request) {
        /* @var $repository \AnimeDb\Bundle\CatalogBundle\Repository\Item */
        $repository = $this->getDoctrine()->getRepository('AnimeDbCatalogBundle:Item');

        // get store item
        $item = $request->getSession()->get(self::NAME_ITEM_ADDED);
        if (!($item instanceof Item)) {
            throw $this->createNotFoundException('Not found item for confirm duplicate');
        }

        // confirm duplicate
        if ($request->isMethod('POST')) {
            $request->getSession()->remove(self::NAME_ITEM_ADDED);
            switch ($request->request->get('do')) {
                case 'add':
                    $item->freez($this->getDoctrine());
                    return $this->addItem($item);
                    break;
                case 'cancel':
                default:
                    return $this->redirect($this->generateUrl('home'));
            }
        }

        // re searching for duplicates
        $duplicate = $repository->findDuplicate($item);
        // now there is no duplication
        if (!$duplicate) {
            $item->freez($this->getDoctrine());
            return $this->addItem($item);
        }

        return $this->render('AnimeDbCatalogBundle:Item:duplicate.html.twig', [
            'items' => $duplicate
        ]);
    }

    /**
     * Add item
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function addItem(Item $item)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($item);
        $em->flush();
        return $this->redirect($this->generateUrl(
            'item_show',
            ['id' => $item->getId(), 'name' => $item->getName()]
        ));
    }

    /**
     * List items limit control
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string|integer $total
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function limitControlAction(Request $request, $total = '')
    {
        /* @var $controls \AnimeDb\Bundle\CatalogBundle\Service\Item\ListControls */
        $controls = $this->get('anime_db.item_list_controls');

        if (!is_numeric($total) || $total < 0) {
            $total = $this->getDoctrine()->getRepository('AnimeDbCatalogBundle:Item')->count();
        }

        return $this->render('AnimeDbCatalogBundle:Item:list_controls/limit.html.twig', [
            'limits' => $controls->getLimits($request->query->all()),
            'total' => $total
        ]);
    }

    /**
     * List items sort control
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sortControlAction(Request $request)
    {
        /* @var $controls \AnimeDb\Bundle\CatalogBundle\Service\Item\ListControls */
        $controls = $this->get('anime_db.item_list_controls');

        $direction = $controls->getSortDirection($request->query->all());
        $sort_direction['type'] = $direction == 'ASC' ? 'DESC' : 'ASC';
        $sort_direction['link'] = $controls->getSortDirectionLink($request->query->all());

        return $this->render('AnimeDbCatalogBundle:Item:list_controls/sort.html.twig', [
            'sort_by' => $controls->getSortColumns($request->query->all()),
            'sort_direction' => $sort_direction
        ]);
    }
}
