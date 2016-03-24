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

use AnimeDb\Bundle\CatalogBundle\Entity\Item;
use AnimeDb\Bundle\CatalogBundle\Entity\Name;
use AnimeDb\Bundle\CatalogBundle\Repository\Item as ItemRepository;
use AnimeDb\Bundle\CatalogBundle\Service\Item\ListControls;
use AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Manager;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AnimeDb\Bundle\CatalogBundle\Form\Type\SearchSimple;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Settings\General as GeneralForm;
use AnimeDb\Bundle\CatalogBundle\Entity\Settings\General as GeneralEntity;
use AnimeDb\Bundle\CatalogBundle\Entity\Search as SearchEntity;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Chain as ChainSearch;
use Symfony\Component\HttpFoundation\Response;

/**
 * Main page of the catalog
 *
 * @package AnimeDb\Bundle\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class HomeController extends BaseController
{
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
     * @var int
     */
    const AUTOCOMPLETE_LIMIT = 10;

    /**
     * Home
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $response = $this->getCacheTimeKeeper()->getResponse('AnimeDbCatalogBundle:Item');
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        // current page for paging
        $page = $request->get('page', 1);
        $current_page = $page > 1 ? $page : 1;

        /* @var $rep ItemRepository */
        $rep = $this->getDoctrine()->getRepository('AnimeDbCatalogBundle:Item');
        /* @var $controls ListControls */
        $controls = $this->get('anime_db.item.list_controls');

        $pagination = null;
        // show not all items
        if ($limit = $controls->getLimit($request->query->all())) {
            $that = $this;
            $query = $request->query->all();
            unset($query['page']);
            $pagination = $this->get('anime_db.pagination')
                ->create(ceil($rep->count()/$limit), $current_page)
                ->setPageLink(function ($page) use ($that, $query) {
                    return $that->generateUrl('home', array_merge($query, ['page' => $page]));
                })
                ->setFerstPageLink($this->generateUrl('home', $query))
                ->getView();
        }

        // get items
        $items = $rep->getList($limit, ($current_page - 1) * $limit);

        return $this->render('AnimeDbCatalogBundle:Home:index.html.twig', [
            'items' => $items,
            'pagination' => $pagination,
            'widget_top' => self::WIDGET_PALCE_TOP,
            'widget_bottom' => self::WIDGET_PALCE_BOTTOM
        ], $response);
    }

    /**
     * Search simple form
     *
     * @return Response
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
     * @param Request $request
     *
     * @return Response
     */
    public function autocompleteNameAction(Request $request)
    {
        /* @var $response JsonResponse */
        $response = $this->getCacheTimeKeeper()
            ->getResponse('AnimeDbCatalogBundle:Item', -1, new JsonResponse());
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        $term = mb_strtolower($request->get('term'), 'UTF8');
        /* @var $service Manager */
        $service = $this->get('anime_db.item.search');
        $result = $service->searchByName($term, self::AUTOCOMPLETE_LIMIT);

        $list = [];
        /* @var $item Item */
        foreach ($result as $item) {
            if (strpos(mb_strtolower($item->getName(), 'UTF8'), $term) === 0) {
                $list[] = $item->getName();
            } else {
                /* @var $name Name */
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
     * @param Request $request
     *
     * @return Response
     */
    public function searchAction(Request $request)
    {
        $response = $this->getCacheTimeKeeper()
            ->getResponse(['AnimeDbCatalogBundle:Item', 'AnimeDbCatalogBundle:Storage']);
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        /* @var $form Form */
        $form = $this->createForm('anime_db_catalog_search_items', new SearchEntity())->handleRequest($request);
        $pagination = null;
        $result = ['list' => [], 'total' => 0];

        if ($form->isValid()) {
            /* @var $controls ListControls */
            $controls = $this->get('anime_db.item.list_controls');

            // current page for paging
            $current_page = $request->get('page', 1);
            $current_page = $current_page > 1 ? $current_page : 1;

            // get items limit
            $limit = $controls->getLimit($request->query->all());

            // do search
            $result = $this->get('anime_db.item.search')->search(
                $form->getData(),
                $limit,
                ($current_page - 1) * $limit,
                $controls->getSortColumn($request->query->all()),
                $controls->getSortDirection($request->query->all())
            );

            if ($limit) {
                // build pagination
                $that = $this;
                $query = $request->query->all();
                unset($query['page']);
                $pagination = $this->get('anime_db.pagination')
                    ->create(ceil($result['total']/$limit), $current_page)
                    ->setPageLink(function ($page) use ($that, $query) {
                        return $that->generateUrl('home_search', array_merge($query, ['page' => $page]));
                    })
                    ->setFerstPageLink($this->generateUrl('home_search', $query))
                    ->getView();
            }
        }

        return $this->render('AnimeDbCatalogBundle:Home:search.html.twig', [
            'form'  => $form->createView(),
            'items' => $result['list'],
            'total' => $result['total'],
            'pagination' => $pagination,
            'searched' => !!$request->query->count()
        ], $response);
    }

    /**
     * General settings
     *
     * @param Request $request
     *
     * @return Response
     */
    public function settingsAction(Request $request)
    {
        $response = $this->getCacheTimeKeeper()->getResponse();
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        $entity = (new GeneralEntity())
            ->setTaskScheduler($this->container->getParameter('task_scheduler.enabled'))
            ->setDefaultSearch($this->container->getParameter('anime_db.catalog.default_search'))
            ->setLocale($request->getLocale());

        /* @var $chain ChainSearch */
        $chain = $this->get('anime_db.plugin.search_fill');
        /* @var $form Form */
        $form = $this->createForm(new GeneralForm($chain), $entity)
            ->handleRequest($request);

        if ($form->isValid()) {
            // update params
            $this->get('anime_db.manipulator.parameters')
                ->set('task_scheduler.enabled', $entity->getTaskScheduler());
            $this->get('anime_db.manipulator.parameters')
                ->set('anime_db.catalog.default_search', $entity->getDefaultSearch());
            $this->get('anime_db.manipulator.parameters')
                ->set('locale', $entity->getLocale());
            $this->get('anime_db.app.listener.request')->setLocale($request, $entity->getLocale());
            $this->get('anime_db.cache_clearer')->clear();

            return $this->redirect($this->generateUrl('home_settings'));
        }

        return $this->render('AnimeDbCatalogBundle:Home:settings.html.twig', [
            'form'  => $form->createView()
        ], $response);
    }
}
