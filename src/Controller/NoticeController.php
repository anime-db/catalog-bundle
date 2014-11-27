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
use AnimeDb\Bundle\AppBundle\Entity\Notice;
use Symfony\Component\HttpFoundation\Request;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Notice\Change as ChangeNotice;

/**
 * Notice
 *
 * @package AnimeDb\Bundle\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class NoticeController extends Controller
{
    /**
     * Number of notices per page
     *
     * @var integer
     */
    const NOTICE_PER_PAGE = 30;

    /**
     * Edit list notices
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $response = $this->get('cache_time_keeper')->getResponse('AnimeDbAppBundle:Notice');
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        $repository = $this->getRepository();
        $change_form = $this->createForm(new ChangeNotice())->handleRequest($request);
        if ($change_form->isValid() && ($notices = $change_form->getData()['notices'])) {
            switch ($change_form->getData()['action']) {
                case ChangeNotice::ACTION_SET_STATUS_SHOWN:
                    $repository->setStatus($notices->toArray(), Notice::STATUS_SHOWN);
                    break;
                case ChangeNotice::ACTION_SET_STATUS_CLOSED:
                    $repository->setStatus($notices->toArray(), Notice::STATUS_CLOSED);
                    break;
                case ChangeNotice::ACTION_REMOVE:
                    $repository->remove($notices->toArray());
            }
            return $this->redirect($this->generateUrl('notice_list'));
        }

        return $this->render('AnimeDbCatalogBundle:Notice:index.html.twig', [
            'has_notices' => $repository->count()
        ], $response);
    }

    /**
     * Get notice list
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        $current_page = $request->get('page', 1);
        $current_page = $current_page > 1 ? $current_page : 1;
        $repository = $this->getRepository();

        // filter list notice
        $filter = $this->createForm('anime_db_catalog_notices_filter')->handleRequest($request);
        if ($filter->isValid()) {
            $query = $repository->getFilteredQuery($filter->getData()['status'], $filter->getData()['type']);
        } else {
            $query = $repository->createQueryBuilder('n');
        }
        $query
            ->orderBy('n.date_created', 'DESC')
            ->setFirstResult(($current_page - 1) * self::NOTICE_PER_PAGE)
            ->setMaxResults(self::NOTICE_PER_PAGE);
        $list = $query->getQuery()->getResult();

        // get count all items
        $count = $query
            ->select('COUNT(n)')
            ->getQuery()
            ->getSingleScalarResult();

        // pagination
        $that = $this;
        $request_query = $request->query->all();
        unset($request_query['page']);
        $pagination = $this->get('anime_db.pagination')
            ->create(ceil($count/self::NOTICE_PER_PAGE), $current_page)
            ->setPageLink(function ($page) use ($that, $request_query) {
                return $that->generateUrl('notice_list', array_merge($request_query, ['page' => $page]));
            })
            ->setFerstPageLink($this->generateUrl('notice_list', $request_query))
            ->getView();

        return $this->render('AnimeDbCatalogBundle:Notice:list.html.twig', [
            'list' => $list,
            'pagination' => $pagination,
            'change_form' => $this->createForm(new ChangeNotice())->createView(),
            'action_remove' => ChangeNotice::ACTION_REMOVE
        ]);
    }

    /**
     * Get repository
     *
     * @return \AnimeDb\Bundle\AppBundle\Repository\Notice
     */
    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('AnimeDbAppBundle:Notice');
    }
}
