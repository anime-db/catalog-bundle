<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CatalogBundle\Controller;

use AnimeDb\Bundle\CatalogBundle\Repository\Label;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;

/**
 * Label.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class LabelController extends BaseController
{
    /**
     * Edit labels.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $response = $this->getCacheTimeKeeper()->getResponse('AnimeDbCatalogBundle:Label');
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        /* @var $rep Label */
        $rep = $this->getDoctrine()->getManager()->getRepository('AnimeDbCatalogBundle:Label');

        $form = $this->createForm('anime_db_catalog_labels', ['labels' => $rep->findAll()])
            ->handleRequest($request);
        if ($form->isValid()) {
            $rep->updateListLabels(new ArrayCollection($form->getData()['labels']));

            return $this->redirect($this->generateUrl('label'));
        }

        return $this->render('AnimeDbCatalogBundle:Label:index.html.twig', [
            'form' => $form->createView(),
        ], $response);
    }
}
