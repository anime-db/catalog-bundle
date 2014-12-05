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
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Label
 *
 * @package AnimeDb\Bundle\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class LabelController extends Controller
{
    /**
     * Edit labels
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request) {
        $response = $this->get('cache_time_keeper')->getResponse('AnimeDbCatalogBundle:Label');
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        /* @var $repository \AnimeDb\Bundle\CatalogBundle\Repository\Label */
        $repository = $this->getDoctrine()->getManager()->getRepository('AnimeDbCatalogBundle:Label');

        $form = $this->createForm('anime_db_catalog_labels', ['labels' => $repository->findAll()])
            ->handleRequest($request);
        if ($form->isValid()) {
            $repository->updateListLabels(new ArrayCollection($form->getData()['labels']));
            return $this->redirect($this->generateUrl('label'));
        }

        return $this->render('AnimeDbCatalogBundle:Label:index.html.twig', [
            'form'  => $form->createView()
        ], $response);
    }
}
