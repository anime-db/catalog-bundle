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
use AnimeDb\Bundle\CatalogBundle\Entity\Item;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;
use AnimeDb\Bundle\CatalogBundle\Form\Type\Plugin\Search;

/**
 * Fill
 *
 * @package AnimeDb\Bundle\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class FillController extends Controller
{
    /**
     * Create new item from source fill
     *
     * @param string $plugin
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function fillerAction($plugin, Request $request)
    {
        $response = $this->get('cache_time_keeper')->getResponse();
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        /* @var $chain \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Chain */
        $chain = $this->get('anime_db.plugin.filler');
        if (!($filler = $chain->getPlugin($plugin))) {
            throw $this->createNotFoundException('Plugin \''.$plugin.'\' is not found');
        }

        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm($filler->getForm());

        $fill_form = null;
        $form->handleRequest($request);
        if ($form->isValid()) {
            $item = $filler->fill($form->getData());
            if (!($item instanceof Item)) {
                $form->addError(new FormError('Can`t get content from the specified source'));
            } else {
                $fill_form = $this->createForm('anime_db_catalog_entity_item', $item)->createView();
            }
        }

        return $this->render('AnimeDbCatalogBundle:Fill:filler.html.twig', [
            'plugin' => $plugin,
            'plugin_name' => $filler->getTitle(),
            'form' => $form->createView(),
            'fill_form' => $fill_form,
        ], $response);
    }

    /**
     * Search source fill for item
     *
     * @param string $plugin
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchAction($plugin, Request $request)
    {
        $response = $this->get('cache_time_keeper')->getResponse();
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        /* @var $search \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Search */
        if (!($search = $this->get('anime_db.plugin.search_fill')->getPlugin($plugin))) {
            throw $this->createNotFoundException('Plugin \''.$plugin.'\' is not found');
        }

        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm($search->getForm());

        $list = [];
        $form->handleRequest($request);
        if ($form->isValid()) {
            $list = $search->search($form->getData());
        }

        // full page or hinclude
        if ($request->get('hinclude', 0)) {
            $tpl = 'AnimeDbCatalogBundle:Fill:search_hinclude.html.twig';
        } else {
            $tpl = 'AnimeDbCatalogBundle:Fill:search.html.twig';
        }

        return $this->render($tpl, [
            'plugin' => $plugin,
            'plugin_name' => $search->getTitle(),
            'list' => $list,
            'form' => $form->createView()
        ], $response);
    }

    /**
     * Search source fill for item
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchInAllAction(Request $request)
    {
        $response = $this->get('cache_time_keeper')->getResponse();
        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        $names = [];
        $plugins = $this->get('anime_db.plugin.search_fill')->getPlugins();
        /* @var $plugin \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Search */
        foreach ($plugins as $plugin) {
            $names[] = $plugin->getName();
        }
        $response->setEtag(md5(implode(',', $names)));

        // response was not modified for this request
        if (!$request->query->count() && $response->isNotModified($request)) {
            return $response;
        }

        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm(new Search());
        $form->handleRequest($request);

        return $this->render('AnimeDbCatalogBundle:Fill:search_in_all.html.twig', [
            'plugins' => $plugins,
            'form'   => $form->createView()
        ], $response);
    }
}
