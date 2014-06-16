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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AnimeDb\Bundle\AppBundle\Util\Pagination;
use AnimeDb\Bundle\CatalogBundle\Form\Notice\Filter as FilterNotice;

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

        // filter list notice
        $filter = $this->createForm(new FilterNotice(), ['type' => null]);
        if ($request->query->count()) {
            $filter->handleRequest($request);
        }

        // get notices
        $notices = $repository->getList(
            self::NOTICE_PER_PAGE,
            ($current_page - 1) * self::NOTICE_PER_PAGE,
            $filter->getData()['type']
        );

        // remove selected notices if need
        if ($request->isMethod('POST') && $notices) {
            if ($ids = (array)$request->request->get('id', [])) {
                foreach ($ids as $id) {
                    foreach ($notices as $key => $notice) {
                        if ($notice->getId() == $id) {
                            $em->remove($notice);
                            unset($notices[$key]);
                            break;
                        }
                    }
                }
                $em->flush();
            }
            return $this->redirect($this->generateUrl('notice_list', $current_page ? ['page' => $current_page] : []));
        }

        // get count all items
        $count = $repository->count($filter->getData()['type']);

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
            'filter' => $filter->getData()['type'] || $count ? $filter->createView() : false
        ]);
    }
}