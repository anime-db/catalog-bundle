<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CatalogBundle\Controller;

use AnimeDb\Bundle\CatalogBundle\Plugin\Import\ImportInterface;
use AnimeDb\Bundle\CatalogBundle\Service\Item\ListControls;
use AnimeDb\Bundle\CatalogBundle\Entity\Item;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage;
use AnimeDb\Bundle\CatalogBundle\Repository\Item as ItemRepository;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Chain as ChainSearch;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Item.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ItemController extends BaseController
{
    /**
     * Name of session to store item to be added.
     *
     * @var string
     */
    const NAME_ITEM_ADDED = '_item_added';

    /**
     * Widget place in content.
     *
     * @var string
     */
    const WIDGET_PALCE_IN_CONTENT = 'item.in_content';

    /**
     * Widget place right.
     *
     * @var string
     */
    const WIDGET_PALCE_RIGHT = 'item.right';

    /**
     * Widget place bottom.
     *
     * @var string
     */
    const WIDGET_PALCE_BOTTOM = 'item.bottom';

    /**
     * Items per page.
     *
     * @var int
     */
    const ITEMS_PER_PAGE = 8;

    /**
     * Default limit.
     *
     * @var int
     */
    const DEFAULT_LIMIT = 8;

    /**
     * Limit for show all items.
     *
     * @var int
     */
    const LIMIT_ALL = 0;

    /**
     * Limit name for show all items.
     *
     * @var int
     */
    const LIMIT_ALL_NAME = 'All (%total%)';

    /**
     * Limits on the number of items per page.
     *
     * @var array
     */
    public static $limits = [8, 16, 32, self::LIMIT_ALL];

    /**
     * Sort items by field.
     *
     * @var array
     */
    public static $sort_by_field = [
        'name' => [
            'title' => 'Item name',
            'name' => 'Name',
        ],
        'date_update' => [
            'title' => 'Last updated item',
            'name' => 'Update',
        ],
        'rating' => [
            'title' => 'Item rating',
            'name' => 'Rating',
        ],
        'date_premiere' => [
            'title' => 'Date premiere',
            'name' => 'Date premiere',
        ],
        'date_end' => [
            'title' => 'End date of issue',
            'name' => 'Date end',
        ],
    ];

    /**
     * @param Item $item
     * @param Request $request
     *
     * @return Response
     */
    public function showAction(Item $item, Request $request)
    {
        $date = [$item->getDateUpdate()];
        // use storage update date
        if ($item->getStorage() instanceof Storage) {
            $date[] = $item->getStorage()->getDateUpdate();
        }
        $response = $this->getCacheTimeKeeper()->getResponse($date);
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        return $this->render('AnimeDbCatalogBundle:Item:show.html.twig', [
            'item' => $item,
            'widget_bottom' => self::WIDGET_PALCE_BOTTOM,
            'widget_in_content' => self::WIDGET_PALCE_IN_CONTENT,
            'widget_right' => self::WIDGET_PALCE_RIGHT,
        ], $response);
    }

    /**
     * Addition form.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function addManuallyAction(Request $request)
    {
        $response = $this->getCacheTimeKeeper()->getResponse();
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        $item = new Item();

        /* @var $form Form */
        $form = $this->createForm('anime_db_catalog_entity_item', $item)
            ->handleRequest($request);
        if ($form->isValid()) {
            /* @var $rep ItemRepository */
            $rep = $this->getDoctrine()->getRepository('AnimeDbCatalogBundle:Item');

            // Add a new entry only if no duplicates
            $duplicate = $rep->findDuplicate($item);
            if ($duplicate) {
                $request->getSession()->set(self::NAME_ITEM_ADDED, $item);

                return $this->redirect($this->generateUrl('item_duplicate'));
            } else {
                return $this->addItem($item);
            }
        }

        return $this->render('AnimeDbCatalogBundle:Item:add-manually.html.twig', [
            'form' => $form->createView(),
        ], $response);
    }

    /**
     * Change item.
     *
     * @param Item $item
     * @param Request $request
     *
     * @return Response
     */
    public function changeAction(Item $item, Request $request)
    {
        /* @var $form Form */
        $form = $this->createForm('anime_db_catalog_entity_item', $item)
            ->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();

            return $this->redirect($this->generateUrl(
                'item_show',
                ['id' => $item->getId(), 'name' => $item->getName()]
            ));
        }

        return $this->render('AnimeDbCatalogBundle:Item:change.html.twig', [
            'item' => $item,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete item.
     *
     * @param Item $item
     *
     * @return Response
     */
    public function deleteAction(Item $item)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($item);
        $em->flush();

        return $this->redirect($this->generateUrl('home'));
    }

    /**
     * Import items.
     *
     * @param string $plugin
     * @param Request $request
     *
     * @return Response
     */
    public function importAction($plugin, Request $request)
    {
        /* @var $chain ChainSearch */
        $chain = $this->get('anime_db.plugin.import');
        /* @var $import ImportInterface */
        if (!($import = $chain->getPlugin($plugin))) {
            throw $this->createNotFoundException('Plugin \''.$plugin.'\' is not found');
        }

        $form = $this->createForm($import->getForm())->handleRequest($request);

        $list = [];
        if ($form->isValid()) {
            // import items
            $list = (array) $import->import($form->getData());

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

        return $this->render('AnimeDbCatalogBundle:Item:import.html.twig', [
            'plugin' => $plugin,
            'items' => $list,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Confirm duplicate item.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function duplicateAction(Request $request)
    {
        /* @var $rep ItemRepository */
        $rep = $this->getDoctrine()->getRepository('AnimeDbCatalogBundle:Item');

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
                    return $this->addItem($item->freez($this->getDoctrine()));
                default:
                    return $this->redirect($this->generateUrl('home'));
            }
        }

        // re searching for duplicates
        $duplicate = $rep->findDuplicate($item);
        // now there is no duplication
        if (!$duplicate) {
            return $this->addItem($item->freez($this->getDoctrine()));
        }

        return $this->render('AnimeDbCatalogBundle:Item:duplicate.html.twig', [
            'items' => $duplicate,
        ]);
    }

    /**
     * Add item.
     *
     * @param Item $item
     *
     * @return Response
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
     * List items limit control.
     *
     * @param Request $request
     * @param string|int $total
     *
     * @return Response
     */
    public function limitControlAction(Request $request, $total = '')
    {
        /* @var $controls ListControls */
        $controls = $this->get('anime_db.item.list_controls');

        if (!is_numeric($total) || $total < 0) {
            /* @var $rep ItemRepository */
            $rep = $this->getDoctrine()->getRepository('AnimeDbCatalogBundle:Item');
            $total = $rep->count();
        }

        return $this->render('AnimeDbCatalogBundle:Item:list_controls/limit.html.twig', [
            'limits' => $controls->getLimits($request->query->all()),
            'total' => $total,
        ]);
    }

    /**
     * List items sort control.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function sortControlAction(Request $request)
    {
        /* @var $controls ListControls */
        $controls = $this->get('anime_db.item.list_controls');

        $direction = $controls->getSortDirection($request->query->all());
        $sort_direction = [
            'type' => $direction == 'ASC' ? 'DESC' : 'ASC',
            'link' => $controls->getSortDirectionLink($request->query->all()),
        ];

        return $this->render('AnimeDbCatalogBundle:Item:list_controls/sort.html.twig', [
            'sort_by' => $controls->getSortColumns($request->query->all()),
            'sort_direction' => $sort_direction,
        ]);
    }
}
