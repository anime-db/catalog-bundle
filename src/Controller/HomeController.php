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
use Symfony\Component\HttpFoundation\JsonResponse;
use AnimeDb\Bundle\CatalogBundle\Form\Type\SearchSimple;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Search as SearchForm;
use AnimeDb\Bundle\AppBundle\Util\Pagination;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Settings\General as GeneralForm;
use AnimeDb\Bundle\CatalogBundle\Entity\Settings\General as GeneralEntity;
use Symfony\Component\Yaml\Yaml;
use AnimeDb\Bundle\CatalogBundle\Entity\Search as SearchEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Main page of the catalog
 *
 * @package AnimeDb\Bundle\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class HomeController extends Controller
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
     * @var integer
     */
    const AUTOCOMPLETE_LIMIT = 10;

    /**
     * Home
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $response = $this->get('cache_time_keeper')->getResponse('AnimeDbCatalogBundle:Item');
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        // current page for paging
        $page = $request->get('page', 1);
        $current_page = $page > 1 ? $page : 1;

        /* @var $repository \AnimeDb\Bundle\CatalogBundle\Repository\Item */
        $repository = $this->getDoctrine()->getRepository('AnimeDbCatalogBundle:Item');
        /* @var $controls \AnimeDb\Bundle\CatalogBundle\Service\Item\ListControls */
        $controls = $this->get('anime_db.item.list_controls');

        $pagination = null;
        // show not all items
        if ($limit = $controls->getLimit($request->query->all())) {
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
        $items = $repository->getList($limit, ($current_page - 1) * $limit);

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
        $response = $this->get('cache_time_keeper')
            ->getResponse('AnimeDbCatalogBundle:Item', -1, new JsonResponse());
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        $term = mb_strtolower($request->get('term'), 'UTF8');
        /* @var $service \AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Manager */
        $service = $this->get('anime_db.item.search');
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
        $response = $this->get('cache_time_keeper')
            ->getResponse(['AnimeDbCatalogBundle:Item', 'AnimeDbCatalogBundle:Storage']);
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        $data = new SearchEntity();
        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm('anime_db_catalog_search_items', $data);
        $items = [];
        $pagination = null;
        $total = 0;

        if ($request->query->count()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                /* @var $service \AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Manager */
                $service = $this->get('anime_db.item.search');
                /* @var $controls \AnimeDb\Bundle\CatalogBundle\Service\Item\ListControls */
                $controls = $this->get('anime_db.item.list_controls');

                // current page for paging
                $current_page = $request->get('page', 1);
                $current_page = $current_page > 1 ? $current_page : 1;

                // get items limit
                $limit = $controls->getLimit($request->query->all());

                // get order
                $current_sort_by = $controls->getSortColumn($request->query->all());
                $current_sort_direction = $controls->getSortDirection($request->query->all());

                // do search
                $result = $service->search(
                    $data,
                    $limit,
                    ($current_page - 1) * $limit,
                    $current_sort_by,
                    $current_sort_direction
                );
                $items = $result['list'];
                $total = $result['total'];

                if ($limit) {
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
            }
        }

        return $this->render('AnimeDbCatalogBundle:Home:search.html.twig', [
            'form'  => $form->createView(),
            'items' => $items,
            'total' => $total,
            'pagination' => $pagination,
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
        $response = $this->get('cache_time_keeper')->getResponse();
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        $entity = new GeneralEntity();
        $entity->setSerialNumber($this->container->getParameter('serial_number'));
        $entity->setTaskScheduler($this->container->getParameter('task_scheduler.enabled'));
        $entity->setDefaultSearch($this->container->getParameter('anime_db.catalog.default_search'));
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
                $parameters['parameters']['anime_db.catalog.default_search'] = $entity->getDefaultSearch();
                $parameters['parameters']['last_update'] = gmdate('r');
                file_put_contents($file, Yaml::dump($parameters)); 
                // change locale
                $this->get('anime_db.app.listener.request')->setLocale($request, $entity->getLocale());
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
        $response = $this->get('cache_time_keeper')
            ->getResponse('AnimeDbCatalogBundle:Label', -1, new JsonResponse());
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

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

    /**
     * Edit labels
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function labelsAction(Request $request) {
        $response = $this->get('cache_time_keeper')->getResponse('AnimeDbCatalogBundle:Label');
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        $em = $this->getDoctrine()->getManager();
        $labels = new ArrayCollection($em->getRepository('AnimeDbCatalogBundle:Label')->findAll());

        $form = $this->createForm($this->get('anime_db.form.type.labels'), ['labels' => $labels]);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $new_labels = $form->getData()['labels'];

                // remove labals
                foreach ($labels as $label) {
                    if (!$new_labels->contains($label)) {
                        /* @var $item \AnimeDb\Bundle\CatalogBundle\Entity\Item */
                        foreach ($label->getItems() as $item) {
                            $item->removeLabel($label);
                        }
                        $em->remove($label);
                    }
                }

                // add new labals
                foreach ($new_labels as $label) {
                    if (!$labels->contains($label)) {
                        $em->persist($label);
                    }
                }
                $em->flush();

                return $this->redirect($this->generateUrl('home_labels'));
            }
        }

        return $this->render('AnimeDbCatalogBundle:Home:labels.html.twig', [
            'form'  => $form->createView()
        ], $response);
    }
}
