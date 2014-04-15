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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AnimeDb\Bundle\CatalogBundle\Form\SearchSimple;
use AnimeDb\Bundle\CatalogBundle\Form\Search as SearchForm;
use Doctrine\ORM\Query\Expr;
use AnimeDb\Bundle\AppBundle\Util\Pagination;
use AnimeDb\Bundle\CatalogBundle\Form\Settings\General as GeneralForm;
use AnimeDb\Bundle\CatalogBundle\Entity\Settings\General as GeneralEntity;
use Symfony\Component\Yaml\Yaml;
use AnimeDb\Bundle\CatalogBundle\Service\Listener\Request as RequestListener;
use AnimeDb\Bundle\CatalogBundle\Entity\Search as SearchEntity;

/**
 * Main page of the catalog
 *
 * @package AnimeDb\Bundle\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class HomeController extends Controller
{
    /**
     * Items per page on home page
     *
     * @var integer
     */
    const HOME_ITEMS_PER_PAGE = 8;

    /**
     * Items per page on search page
     *
     * @var integer
     */
    const SEARCH_ITEMS_PER_PAGE = 8;

    /**
     * Limit for show all items
     *
     * @var integer
     */
    const SHOW_LIMIT_ALL = -1;

    /**
     * Limit name for show all items
     *
     * @var integer
     */
    const SHOW_LIMIT_ALL_NAME = 'All (%total%)';

    /**
     * Widget place top
     *
     * @var string
     */
    const WIDGET_PALCE_TOP = 'home.top';

    /**
     * Widget place bottom
     *
     * @var string
     */
    const WIDGET_PALCE_BOTTOM = 'home.bottom';

    /**
     * Autocomplete list limit
     *
     * @var integer
     */
    const AUTOCOMPLETE_LIMIT = 10;

    /**
     * Limits on the number of items per page for home page
     *
     * @var array
     */
    public static $home_show_limit = [8, 16, 32, self::SHOW_LIMIT_ALL];

    /**
     * Limits on the number of items per page for search page
     *
     * @var array
     */
    public static $search_show_limit = [8, 16, 32, self::SHOW_LIMIT_ALL];

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
     * Sort direction
     *
     * @var array
     */
    public static $sort_direction = [
        'DESC' => 'Descending',
        'ASC'  => 'Ascending'
    ];

    /**
     * Home
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $response = new Response();
        // caching
        if ($last_update = $this->container->getParameter('last_update')) {
            $response->setLastModified(new \DateTime($last_update));
        }
        // check items last update
        /* @var $repository \AnimeDb\Bundle\CatalogBundle\Repository\Item */
        $repository = $this->getDoctrine()->getRepository('AnimeDbCatalogBundle:Item');
        $last_update = $repository->getLastUpdate();
        if ($response->getLastModified() < $last_update) {
            $response->setLastModified($last_update);
        }
        $total = $repository->count();
        $response->setEtag(md5($total));

        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        // current page for paging
        $page = $request->get('page', 1);
        $current_page = $page > 1 ? $page : 1;

        // get items limit
        $limit = (int)$request->get('limit', self::HOME_ITEMS_PER_PAGE);
        $limit = in_array($limit, self::$home_show_limit) ? $limit : self::HOME_ITEMS_PER_PAGE;

        /* @var $repository \AnimeDb\Bundle\CatalogBundle\Repository\Item */
        $repository = $this->getDoctrine()->getRepository('AnimeDbCatalogBundle:Item');

        $pagination = null;
        // show not all items
        if ($limit != self::SHOW_LIMIT_ALL) {

            $that = $this;
            $pagination = $this->get('anime_db.pagination')->createNavigation(
                ceil($repository->count()/$limit),
                $current_page,
                Pagination::DEFAULT_LIST_LENGTH,
                function ($page) use ($that) {
                    return $that->generateUrl('home', ['page' => $page]);
                },
                $this->generateUrl('home')
            );
        }

        // get items
        $items = $repository->getList(
            ($limit != self::SHOW_LIMIT_ALL ? $limit : 0),
            ($limit != self::SHOW_LIMIT_ALL ? ($current_page - 1) * $limit : 0)
        );

        // assembly parameters limit output
        $show_limit = [];
        foreach (self::$home_show_limit as $value) {
            $show_limit[] = [
                'link' => $this->generateUrl('home', ['limit' => $value]),
                'name' => $value != -1 ? $value : self::SHOW_LIMIT_ALL_NAME,
                'count' => $value,
                'current' => $limit == $value
            ];
        }

        return $this->render('AnimeDbCatalogBundle:Home:index.html.twig', [
            'items' => $items,
            'total' => $total,
            'show_limit' => $show_limit,
            'pagination' => $pagination,
            'widget_top' => self::WIDGET_PALCE_TOP,
            'widget_bottom' => self::WIDGET_PALCE_BOTTOM
        ], $response);
    }

    /**
     * Search simple form
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchSimpleFormAction()
    {
        $form = new SearchSimple($this->generateUrl('home_autocomplete_name'));
        return $this->render('AnimeDbCatalogBundle:Home:searchSimpleForm.html.twig', [
            'form' => $this->createForm($form)->createView(),
        ]);
    }

    /**
     * Autocomplete name
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function autocompleteNameAction(Request $request)
    {
        $response = new JsonResponse();
        // caching
        if ($last_update = $this->container->getParameter('last_update')) {
            $response->setLastModified(new \DateTime($last_update));
        }
        // check items last update
        /* @var $repository \AnimeDb\Bundle\CatalogBundle\Repository\Item */
        $repository = $this->getDoctrine()->getRepository('AnimeDbCatalogBundle:Item');
        $last_update = $repository->getLastUpdate();
        if ($response->getLastModified() < $last_update) {
            $response->setLastModified($last_update);
        }
        $total = $repository->count();
        $response->setEtag(md5($total));

        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        $term = mb_strtolower($request->get('term'), 'UTF8');
        /* @var $service \AnimeDb\Bundle\CatalogBundle\Service\Search\Manager */
        $service = $this->get('anime_db.search');
        $result = $service->searchByName($term, self::AUTOCOMPLETE_LIMIT);

        $list = [];
        /* @var $item \AnimeDb\Bundle\CatalogBundle\Entity\Item */
        foreach ($result as $item) {
            if (strpos(mb_strtolower($item->getName(), 'UTF8'), $term) === 0) {
                $list[] = $item->getName();
            } else {
                /* @var $name \AnimeDb\Bundle\CatalogBundle\Entity\Name */
                foreach ($item->getNames() as $name) {
                    if (strpos(mb_strtolower($name->getName(), 'UTF8'), $term) === 0) {
                        $list[] = $name->getName();
                        break;
                    }
                }
            }
        }

        return $response->setData($list);
    }

    /**
     * Search item
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchAction(Request $request)
    {
        $response = new Response();
        // caching
        if ($last_update = $this->container->getParameter('last_update')) {
            $response->setLastModified(new \DateTime($last_update));
        }
        // check items last update
        if ($request->query->count()) {
            $repository = $this->getDoctrine()->getRepository('AnimeDbCatalogBundle:Item');
            // last item update
            $last_update = $repository->getLastUpdate();
            if ($response->getLastModified() < $last_update) {
                $response->setLastModified($last_update);
            }
            $response->setEtag(md5($repository->count()));

            // last storage update
            $last_update = $this->getDoctrine()
                ->getRepository('AnimeDbCatalogBundle:Storage')
                ->getLastUpdate();
            if ($response->getLastModified() < $last_update) {
                $response->setLastModified($last_update);
            }
        }
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        $data = new SearchEntity();
        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm('anime_db_catalog_search_items', $data);
        $items = [];
        $pagination = null;
        // list items controls
        $show_limit = null;
        $sort_by = null;
        $sort_direction = null;
        $total = 0;

        if ($request->query->count()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                /* @var $service \AnimeDb\Bundle\CatalogBundle\Service\Search\Manager */
                $service = $this->get('anime_db.search');

                // current page for paging
                $current_page = $request->get('page', 1);
                $current_page = $current_page > 1 ? $current_page : 1;

                // get items limit
                $limit = (int)$request->get('limit', self::SEARCH_ITEMS_PER_PAGE);
                $limit = in_array($limit, self::$search_show_limit) ? $limit : self::SEARCH_ITEMS_PER_PAGE;

                // get order
                $current_sort_by = $service->getValidSortColumn($request->get('sort_by'));
                $current_sort_direction = $service->getValidSortDirection($request->get('sort_direction'));

                // do search
                $result = $service->search(
                    $data,
                    ($limit != self::SHOW_LIMIT_ALL ? $limit : 0),
                    ($limit != self::SHOW_LIMIT_ALL ? ($current_page - 1) * $limit : 0),
                    $current_sort_by,
                    $current_sort_direction
                );
                $items = $result['list'];
                $total = $result['total'];

                // build sort params for tamplate
                $sort_by = [];
                foreach (self::$sort_by_field as $field => $info) {
                    $sort_by[] = [
                        'name' => $info['name'],
                        'title' => $info['title'],
                        'current' => $current_sort_by == $field,
                        'link' => $this->generateUrl(
                            'home_search',
                            array_merge($request->query->all(), ['sort_by' => $field])
                        )
                    ];
                }
                $sort_direction['type'] = ($current_sort_direction == 'ASC' ? 'DESC' : 'ASC');
                $sort_direction['link'] = $this->generateUrl(
                    'home_search',
                    array_merge($request->query->all(), ['sort_direction' => $sort_direction['type']])
                );

                if ($limit != self::SHOW_LIMIT_ALL) {
                    // build pagination
                    $query = $request->query->all();
                    if (isset($query['page'])) {
                        unset($query['page']);
                    }
                    $that = $this;
                    $pagination = $this->get('anime_db.pagination')->createNavigation(
                        ceil($total/$limit),
                        $current_page,
                        Pagination::DEFAULT_LIST_LENGTH,
                        function ($page) use ($that, $query) {
                            return $that->generateUrl(
                                'home_search',
                                array_merge($query, ['page' => $page])
                            );
                        },
                        $this->generateUrl('home_search', $query)
                    );
                }

                // assembly parameters limit output
                foreach (self::$search_show_limit as $value) {
                    $show_limit[] = [
                        'link' => $this->generateUrl(
                            'home_search',
                            array_merge($request->query->all(), ['limit' => $value])
                        ),
                        'name' => $value != -1 ? $value : self::SHOW_LIMIT_ALL_NAME,
                        'count' => $value,
                        'current' => !empty($limit) && $limit == $value
                    ];
                }
            }
        }

        return $this->render('AnimeDbCatalogBundle:Home:search.html.twig', [
            'form'  => $form->createView(),
            'items' => $items,
            'total' => $total,
            'show_limit' => $show_limit,
            'pagination' => $pagination,
            'sort_by' => $sort_by,
            'sort_direction' => $sort_direction,
            'searched' => !!$request->query->count()
        ], $response);
    }

    /**
     * General settings
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function settingsAction(Request $request)
    {
        $response = new Response();
        // caching
        if ($last_update = $this->container->getParameter('last_update')) {
            $response->setLastModified(new \DateTime($last_update));

            // response was not modified for this request
            if ($response->isNotModified($request)) {
                return $response;
            }
        }

        $entity = new GeneralEntity();
        $entity->setSerialNumber($this->container->getParameter('serial_number'));
        $entity->setTaskScheduler($this->container->getParameter('task_scheduler.enabled'));
        $entity->setDefaultSearch($this->container->getParameter('default_search'));
        $entity->setLocale($request->getLocale());

        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm(new GeneralForm($this->get('anime_db.plugin.search_fill')), $entity);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                // update params
                $file = $this->container->getParameter('kernel.root_dir').'/config/parameters.yml';
                $parameters = Yaml::parse($file);
                $parameters['parameters']['serial_number'] = $entity->getSerialNumber();
                $parameters['parameters']['task_scheduler.enabled'] = $entity->getTaskScheduler();
                $parameters['parameters']['default_search'] = $entity->getDefaultSearch();
                $parameters['parameters']['last_update'] = gmdate('r');
                file_put_contents($file, Yaml::dump($parameters)); 
                // change locale
                $this->get('anime_db.listener.request')->setLocale($request, $entity->getLocale());
                // clear cache
                $this->get('anime_db.cache_clearer')->clear();

                return $this->redirect($this->generateUrl('home_settings'));
            }
        }

        return $this->render('AnimeDbCatalogBundle:Home:settings.html.twig', [
            'form'  => $form->createView()
        ], $response);
    }

    /**
     * Autocomplete label
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function autocompleteLabelAction(Request $request)
    {
        $response = new JsonResponse();

        $term = mb_strtolower($request->get('term'), 'UTF8');

        // register custom lower()
        $conn = $this->getDoctrine()->getConnection()->getWrappedConnection();
        if (method_exists($conn, 'sqliteCreateFunction')) {
            $conn->sqliteCreateFunction('lower', function ($str) {
                return mb_strtolower($str, 'UTF8');
            }, 1);
        }

        $list = $this->getDoctrine()->getManager()->createQuery('
            SELECT
                l
            FROM
                AnimeDbCatalogBundle:Label l
            WHERE
                LOWER(l.name) LIKE :name
        ')
            ->setParameter('name', preg_replace('/%+/', '%%', $term).'%')
            ->getResult();

        /* @var $label \AnimeDb\Bundle\CatalogBundle\Entity\Label */
        foreach ($list as $key => $label) {
            $list[$key] = $label->getName();
        }

        return $response->setData($list);
    }
}