<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Controller;

use AnimeDb\Bundle\AppBundle\Entity\Notice;
use AnimeDb\Bundle\AppBundle\Repository\Notice as NoticeRepository;
use Symfony\Component\HttpFoundation\Request;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Notice\Change as ChangeNotice;
use Symfony\Component\HttpFoundation\Response;

/**
 * Notice.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class NoticeController extends BaseController
{
    /**
     * Number of notices per page.
     *
     * @var int
     */
    const NOTICE_PER_PAGE = 30;

    /**
     * Edit list notices.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $response = $this->getCacheTimeKeeper()->getResponse('AnimeDbAppBundle:Notice');
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        $rep = $this->getRepository();
        $change_form = $this->createForm(new ChangeNotice())->handleRequest($request);
        if ($change_form->isValid() && ($notices = $change_form->getData()['notices'])) {
            switch ($change_form->getData()['action']) {
                case ChangeNotice::ACTION_SET_STATUS_SHOWN:
                    $rep->setStatus($notices->toArray(), Notice::STATUS_SHOWN);
                    break;
                case ChangeNotice::ACTION_SET_STATUS_CLOSED:
                    $rep->setStatus($notices->toArray(), Notice::STATUS_CLOSED);
                    break;
                case ChangeNotice::ACTION_REMOVE:
                    $rep->remove($notices->toArray());
            }

            return $this->redirect($this->generateUrl('notice_list'));
        }

        return $this->render('AnimeDbCatalogBundle:Notice:index.html.twig', [
            'has_notices' => $rep->count(),
        ], $response);
    }

    /**
     * Get notice list.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request)
    {
        $current_page = $request->get('page', 1);
        $current_page = $current_page > 1 ? $current_page : 1;
        $rep = $this->getRepository();

        // filter list notice
        $filter = $this->createForm('notices_filter')->handleRequest($request);
        if ($filter->isValid()) {
            $query = $rep->getFilteredQuery($filter->getData()['status'], $filter->getData()['type']);
        } else {
            $query = $rep->createQueryBuilder('n');
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
            ->create(ceil($count / self::NOTICE_PER_PAGE), $current_page)
            ->setPageLink(function ($page) use ($that, $request_query) {
                return $that->generateUrl('notice_list', array_merge($request_query, ['page' => $page]));
            })
            ->setFirstPageLink($this->generateUrl('notice_list', $request_query))
            ->getView();

        return $this->render('AnimeDbCatalogBundle:Notice:list.html.twig', [
            'list' => $list,
            'pagination' => $pagination,
            'change_form' => $this->createForm(new ChangeNotice())->createView(),
            'filter' => $filter->createView(),
            'action_remove' => ChangeNotice::ACTION_REMOVE,
        ]);
    }

    /**
     * @return NoticeRepository
     */
    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('AnimeDbAppBundle:Notice');
    }
}
