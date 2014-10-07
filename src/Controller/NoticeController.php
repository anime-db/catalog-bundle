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
use AnimeDb\Bundle\AppBundle\Util\Pagination;
use AnimeDb\Bundle\CatalogBundle\Form\Notice\Change as ChangeNotice;

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

        $em = $this->getDoctrine()->getManager();
        /* @var $repository \AnimeDb\Bundle\AppBundle\Repository\Notice */
        $repository = $em->getRepository('AnimeDbAppBundle:Notice');
        $query = $repository->createQueryBuilder('n');

        // filter list notice
        $filter = $this->createForm('anime_db_catalog_notices_filter');
        if ($request->query->count()) {
            $filter->handleRequest($request);
            if ($filter->isValid()) {
                if (!is_null($filter->getData()['status'])) {
                    $query
                        ->where('n.status = :status')
                        ->setParameter('status', $filter->getData()['status']);
                }
                if ($filter->getData()['type']) {
                    $query
                        ->andWhere('n.type = :type')
                        ->setParameter('type', $filter->getData()['type']);
                }
            }
        }

        // get notices
        $notices = clone $query;
        $notices = $notices
            ->orderBy('n.date_created', 'DESC')
            ->setFirstResult(($current_page - 1) * self::NOTICE_PER_PAGE)
            ->setMaxResults(self::NOTICE_PER_PAGE)
            ->getQuery()
            ->getResult();

        // change selected notices if need
        $change_form = $this->createForm(new ChangeNotice($notices));
        if ($request->isMethod('POST') && $notices) {
            $change_form->handleRequest($request);
            if ($change_form->isValid() && ($ids = $change_form->getData()['id'])) {
                foreach ($ids as $id) {
                    /* @var $notice \AnimeDb\Bundle\AppBundle\Entity\Notice */
                    foreach ($notices as $notice) {
                        if ($notice->getId() == $id) {
                            switch ($change_form->getData()['action']) {
                                case ChangeNotice::ACTION_SET_STATUS_SHOWN:
                                    $notice->setStatus(Notice::STATUS_SHOWN);
                                    $em->persist($notice);
                                    break 2;
                                case ChangeNotice::ACTION_SET_STATUS_CLOSED:
                                    $notice->setStatus(Notice::STATUS_CLOSED);
                                    $em->persist($notice);
                                    break 2;
                                case ChangeNotice::ACTION_REMOVE:
                                    $em->remove($notice);
                                    break 2;
                            }
                        }
                    }
                }
                $em->flush();
                return $this->redirect($this->generateUrl('notice_list', $current_page ? ['page' => $current_page] : []));
            }
        }

        // get count all items
        $count = $query
            ->select('COUNT(n)')
            ->getQuery()
            ->getSingleScalarResult();

        // pagination
        $that = $this;
        $query = $request->query->all();
        unset($query['page']);
        $pagination = $this->get('anime_db.pagination')->createNavigation(
            ceil($count/self::NOTICE_PER_PAGE),
            $current_page,
            Pagination::DEFAULT_LIST_LENGTH,
            function ($page) use ($that, $query) {
                return $that->generateUrl('notice_list', array_merge($query, ['page' => $page]));
            },
            $this->generateUrl('notice_list', $query)
        );

        return $this->render('AnimeDbCatalogBundle:Notice:list.html.twig', [
            'list' => $notices,
            'pagination' => $pagination,
            'filter' => $filter->getData()['type'] || $count ? $filter->createView() : false,
            'change_form' => $change_form->createView(),
            'action_remove' => ChangeNotice::ACTION_REMOVE
        ]);
    }
}