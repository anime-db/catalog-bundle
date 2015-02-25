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
use AnimeDb\Bundle\CatalogBundle\Entity\Storage;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Entity\Storage as StorageForm;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Installation controller
 *
 * @package AnimeDb\Bundle\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class InstallController extends Controller
{
    /**
     * Home (Stap #1)
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        // app already installed
        if ($this->container->getParameter('anime_db.catalog.installed')) {
            return $this->redirect($this->generateUrl('home'));
        }

        $response = $this->get('cache_time_keeper')->getResponse();
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }
        $form = $this->createForm('anime_db_catalog_install_locale')->handleRequest($request);

        if ($form->isValid()) {
            // update params
            $this->get('anime_db.manipulator.parameters')
                ->set('locale', $form->getData()['locale']);
            // clear cache
            $this->get('anime_db.cache_clearer')->clear();
            // redirect to step 2
            return $this->redirect($this->generateUrl('install_add_storage'));
        }

        return $this->render('AnimeDbCatalogBundle:Install:index.html.twig', [
            'form' => $form->createView(),
        ], $response);
    }

    /**
     * Add storage (Stap #2)
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addStorageAction(Request $request)
    {
        // app already installed
        if ($this->container->getParameter('anime_db.catalog.installed')) {
            return $this->redirect($this->generateUrl('home'));
        }

        $response = $this->get('cache_time_keeper')->getResponse('AnimeDbCatalogBundle:Storage');
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }
        // get last storage
        $storage = $this->getDoctrine()->getRepository('AnimeDbCatalogBundle:Storage')->getLast();
        $storage = $storage ?: new Storage();

        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm(new StorageForm(), $storage)->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($storage);
            $em->flush();
            // redirect to step 3
            return $this->redirect($this->generateUrl('install_what_you_want'));
        }

        return $this->render('AnimeDbCatalogBundle:Install:add_storage.html.twig', [
            'form' => $form->createView(),
            'is_new' => !$storage->getId()
        ], $response);
    }

    /**
     * What you want
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function whatYouWantAction(Request $request)
    {
        // app already installed
        if ($this->container->getParameter('anime_db.catalog.installed')) {
            return $this->redirect($this->generateUrl('home'));
        }

        $response = $this->get('cache_time_keeper')->getResponse();
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        if ($do = $request->request->get('do')) {
            switch ($do) {
                case 'skip':
                    return $this->redirect($this->generateUrl('install_end', ['from' => 'skip_scan_storage']));
                    break;
                case 'scan':
                    return $this->redirect($this->generateUrl('install_storage_scan'));
                    break;
                case 'sample':
                    // TODO install sample items
                    return $this->redirect($this->generateUrl('install_end', ['from' => 'install_sample']));
                    break;
            }
        }

        return $this->render('AnimeDbCatalogBundle:Install:what_you_want.html.twig', [], $response);
    }

    /**
     * Scan storage (Stap #4)
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function scanAction(Request $request)
    {
        // app already installed
        if ($this->container->getParameter('anime_db.catalog.installed')) {
            return $this->redirect($this->generateUrl('home'));
        }

        $storage = $this->getDoctrine()->getRepository('AnimeDbCatalogBundle:Storage')->getLast();
        if (!$storage) {
            return $this->redirect('install_add_storage');
        }

        $response = $this->get('cache_time_keeper')->getResponse($storage->getDateUpdate());

        $scan_output = $this->container->getParameter('anime_db.catalog.storage.scan_output');
        $scan_output = sprintf($scan_output, $storage->getId());
        if (!is_dir($dir = pathinfo($scan_output, PATHINFO_DIRNAME))) {
            if (true !== @mkdir($dir, 0755, true)) {
                throw new IOException('Unable to create directory for logging output');
            }
        }

        // scan storage in background
        $this->get('anime_db.command')->send(sprintf(
            'php app/console animedb:scan-storage --no-ansi --export=%s %s >%s 2>&1',
            sprintf($this->container->getParameter('anime_db.catalog.storage.scan_progress'), $storage->getId()),
            $storage->getId(),
            $scan_output
        ));

        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        return $this->render('AnimeDbCatalogBundle:Install:scan.html.twig', [
            'storage' => $storage
        ], $response);
    }

    /**
     * End install (Stap #5)
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function endAction(Request $request)
    {
        // app already installed
        if ($this->container->getParameter('anime_db.catalog.installed')) {
            return $this->redirect($this->generateUrl('home'));
        }

        $response = $this->get('cache_time_keeper')->getResponse();
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        if ($request->isMethod('POST')) {
            // update params
            $this->get('anime_db.manipulator.parameters')
                ->set('anime_db.catalog.installed', true);
            // clear cache
            $this->get('anime_db.cache_clearer')->clear();
            return $this->redirect('home');
        }

        return $this->render('AnimeDbCatalogBundle:Install:end.html.twig', [], $response);
    }
}
