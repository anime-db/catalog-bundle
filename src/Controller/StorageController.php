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
use AnimeDb\Bundle\CatalogBundle\Form\Type\Entity\Storage as StorageForm;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Filesystem\Exception\IOException;

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
        $response = $this->get('cache_time_keeper')->getResponse('AnimeDbCatalogBundle:Storage');
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
        $response = $this->get('cache_time_keeper')->getResponse($storage->getDateUpdate());
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
        $response = $this->get('cache_time_keeper')->getResponse();
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
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
        /* @var $response \Symfony\Component\HttpFoundation\JsonResponse */
        $response = $this->get('cache_time_keeper')
            ->getResponse('AnimeDbCatalogBundle:Storage', -1, new JsonResponse());
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        /* @var $storage \AnimeDb\Bundle\CatalogBundle\Entity\Storage */
        $storage = $this->getDoctrine()->getManager()
            ->find('AnimeDbCatalogBundle:Storage', $request->get('id'));

        return $response->setData([
            'required' => $storage->isPathRequired(),
            'path' => $storage->getPath()
        ]);
    }

    /**
     * Scan storage
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Storage $storage
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function scanAction(Storage $storage, Request $request)
    {
        $response = $this->get('cache_time_keeper')->getResponse($storage->getDateUpdate());

        $scan_output = $this->container->getParameter('anime_db.catalog.storage.scan_output');
        $scan_output = sprintf($scan_output, $storage->getId());
        if (!is_dir($dir = pathinfo($scan_output, PATHINFO_DIRNAME))) {
            if (true !== @mkdir($dir, 0755, true)) {
                throw new IOException('Unable to create directory for logging output');
            }
        }

        // scan storage in background
        $this->get('anime_db.command')->exec(sprintf(
            'php app/console animedb:scan-storage --no-ansi --export=%s %s >%s 2>&1',
            sprintf($this->container->getParameter('anime_db.catalog.storage.scan_progress'), $storage->getId()),
            $storage->getId(),
            $scan_output
        ));

        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        return $this->render('AnimeDbCatalogBundle:Storage:scan.html.twig', [
            'storage' => $storage
        ], $response);
    }

    /**
     * Get storage scan output
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Storage $storage
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function scanOutputAction(Storage $storage, Request $request)
    {
        $filename = $this->container->getParameter('anime_db.catalog.storage.scan_output');
        $filename = sprintf($filename, $storage->getId());
        if (!file_exists($filename)) {
            throw $this->createNotFoundException('Log file is not found');
        }

        $log = file_get_contents($filename);
        $is_end = preg_match('/\nTime: \d+ s./', $log);

        if (($offset = $request->query->get('offset', 0)) && is_numeric($offset) && $offset > 0) {
            $log = (string)mb_substr($log, $offset, mb_strlen($log, 'UTF-8')-$offset, 'UTF-8');
        }

        return new JsonResponse(['content' => $log, 'end' => $is_end]);
    }

    /**
     * Get storage scan progress
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Storage $storage
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function scanProgressAction(Storage $storage)
    {
        $filename = $this->container->getParameter('anime_db.catalog.storage.scan_progress');
        $filename = sprintf($filename, $storage->getId());
        if (!file_exists($filename)) {
            throw $this->createNotFoundException('The progress status cannot be read');
        }

        $log = trim(file_get_contents($filename), " \r\n%");
        return new JsonResponse(['status' => ($log != '' ? intval($log) : 100)]);
    }
}
