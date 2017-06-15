<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Controller;

use AnimeDb\Bundle\CatalogBundle\Entity\Storage;
use AnimeDb\Bundle\CatalogBundle\Repository\Storage as StorageRepository;
use AnimeDb\Bundle\CatalogBundle\Service\Storage\Scan\LogResponse;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Entity\Storage as StorageForm;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Storages.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class StorageController extends BaseController
{
    /**
     * Link to guide, how add a new storage.
     *
     * @var string
     */
    const GUIDE_LINK = '/guide/storage/add.html';

    /**
     * Storage list.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request)
    {
        $response = $this->getCacheTimeKeeper()->getResponse('AnimeDbCatalogBundle:Storage');
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        /* @var $rep StorageRepository */
        $rep = $this->getDoctrine()->getRepository('AnimeDbCatalogBundle:Storage');

        return $this->render('AnimeDbCatalogBundle:Storage:list.html.twig', [
            'storages' => $rep->getList(),
        ], $response);
    }

    /**
     * Change storage.
     *
     * @param Storage $storage
     * @param Request $request
     *
     * @return Response
     */
    public function changeAction(Storage $storage, Request $request)
    {
        $response = $this->getCacheTimeKeeper()->getResponse($storage->getDateUpdate());
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        /* @var $form Form */
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
            'form' => $form->createView(),
        ], $response);
    }

    /**
     * Add storage.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function addAction(Request $request)
    {
        $storage = new Storage();

        /* @var $form Form */
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
            'form' => $form->createView(),
            'guide' => $this->get('anime_db.api.client')->getSiteUrl(self::GUIDE_LINK),
        ]);
    }

    /**
     * Delete storage.
     *
     * @param Storage $storage
     *
     * @return Response
     */
    public function deleteAction(Storage $storage)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($storage);
        $em->flush();

        return $this->redirect($this->generateUrl('storage_list'));
    }

    /**
     * Get storage path.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getPathAction(Request $request)
    {
        /* @var $response JsonResponse */
        $response = $this->getCacheTimeKeeper()
            ->getResponse('AnimeDbCatalogBundle:Storage', -1, new JsonResponse());
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        /* @var $storage Storage */
        $storage = $this->getDoctrine()->getManager()
            ->find('AnimeDbCatalogBundle:Storage', $request->get('id'));

        return $response->setData([
            'required' => $storage->isPathRequired(),
            'path' => $storage->getPath(),
        ]);
    }

    /**
     * Scan storage.
     *
     * @param Storage $storage
     *
     * @return Response
     */
    public function scanAction(Storage $storage)
    {
        $this->get('anime_db.storage.scan_executor')->export($storage);

        return $this->render('AnimeDbCatalogBundle:Storage:scan.html.twig', [
            'storage' => $storage,
        ]);
    }

    /**
     * Get storage scan output.
     *
     * @param Storage $storage
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function scanOutputAction(Storage $storage, Request $request)
    {
        $filename = $this->container->getParameter('anime_db.catalog.storage.scan_output');
        $filename = sprintf($filename, $storage->getId());
        if (!file_exists($filename)) {
            throw $this->createNotFoundException('Log file is not found');
        }

        $log = file_get_contents($filename);
        $is_end = $this->isEndOfLog($log);

        // force stop scan progress
        if ($is_end) {
            $this->get('anime_db.storage.scan_executor')->forceStopScan($storage);
        }

        return LogResponse::logOffset($log, $request->query->get('offset', 0), $is_end);
    }

    /**
     * @param string $log
     *
     * @return bool
     */
    private function isEndOfLog($log)
    {
        $is_end = preg_match('/\nTime: \d+ s./', $log);

        // end of execute scan on Windows
        $root = realpath($this->getParameter('kernel.root_dir').'/../');
        $is_end_win = preg_match('/\n'.preg_quote($root).'>/', $log);

        // detect fatal error in log
        $is_end_error = strpos($log, 'Fatal error: ') != false;

        return $is_end || $is_end_win || $is_end_error;
    }

    /**
     * Get storage scan progress.
     *
     * @param Storage $storage
     *
     * @return JsonResponse
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
