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
use AnimeDb\Bundle\CatalogBundle\Entity\Storage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AnimeDb\Bundle\CatalogBundle\Form\Entity\Storage as StorageForm;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Storages
 *
 * @package AnimeDb\Bundle\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class StorageController extends Controller
{
    /**
     * Storages list
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        $response = new Response();
        // caching
        if ($last_update = $this->container->getParameter('last_update')) {
            $response->setLastModified(new \DateTime($last_update));
        }
        // last storage update
        $repository = $this->getDoctrine()->getRepository('AnimeDbCatalogBundle:Storage');
        $last_update = $repository->getLastUpdate();
        if ($response->getLastModified() < $last_update) {
            $response->setLastModified($last_update);
        }
        $response->setEtag(md5($repository->count()));

        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        /* @var $repository \AnimeDb\Bundle\CatalogBundle\Repository\Storage */
        $repository = $this->getDoctrine()->getRepository('AnimeDbCatalogBundle:Storage');
        return $this->render('AnimeDbCatalogBundle:Storage:list.html.twig', [
            'storages' => $repository->getList()
        ], $response);
    }

    /**
     * Change storages
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Storage $storage
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changeAction(Storage $storage, Request $request)
    {
        $response = new Response();
        // caching
        if ($last_update = $this->container->getParameter('last_update')) {
            $response->setLastModified(new \DateTime($last_update));
        }
        // use storage update date
        if ($response->getLastModified() < $storage->getDateUpdate()) {
            $response->setLastModified($storage->getDateUpdate());
        }
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm(new StorageForm(), $storage);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($storage);
                $em->flush();
                return $this->redirect($this->generateUrl('storage_list'));
            }
        }

        return $this->render('AnimeDbCatalogBundle:Storage:change.html.twig', [
            'storage' => $storage,
            'form' => $form->createView()
        ], $response);
    }

    /**
     * Add storages
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request)
    {
        $response = new Response();
        // caching
        if (($last_update = $this->container->getParameter('last_update')) && !$request->query->count()) {
            $response->setLastModified(new \DateTime($last_update));

            // response was not modified for this request
            if ($response->isNotModified($request)) {
                return $response;
            }
        }

        $storage = new Storage();

        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm(new StorageForm(), $storage);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($storage);
                $em->flush();

                // scan storage
                $this->get('anime_db.command')
                    ->exec('php app/console animedb:scan-storage '.$storage->getId().' >/dev/null 2>&1');
                return $this->redirect($this->generateUrl('storage_list'));
            }
        }

        return $this->render('AnimeDbCatalogBundle:Storage:add.html.twig', [
            'form' => $form->createView()
        ], $response);
    }

    /**
     * Delete storages
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Storage $storage
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Storage $storage)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($storage);
        $em->flush();
        return $this->redirect($this->generateUrl('storage_list'));
    }

    /**
     * Get storage path
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getPathAction(Request $request)
    {
        /* @var $storage \AnimeDb\Bundle\CatalogBundle\Entity\Storage */
        $storage = $this->getDoctrine()->getManager()
            ->find('AnimeDbCatalogBundle:Storage', $request->get('id'));

        return new JsonResponse([
            'required' => $storage->isPathRequired(),
            'path' => $storage->getPath()
        ]);
    }
}