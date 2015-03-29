<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Event\Listener;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Request listener
 *
 * @package AnimeDb\Bundle\AppBundle\Event\Listener
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Request
{
    /**
     * Locale
     *
     * @var string|null
     */
    protected $locale;

    /**
     * Router
     *
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    protected $router;

    /**
     * Construct
     *
     * @param string|null $locale
     * @param \Symfony\Bundle\FrameworkBundle\Routing\Router $router
     */
    public function __construct($locale, Router $router)
    {
        $this->locale = $locale;
        $this->router = $router;
    }

    /**
     * Kernel request handler
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($this->locale || $event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        // get route name from request
        $route = $this->router->matchRequest($event->getRequest());
        if (empty($route['_route'])) {
            return;
        }
        $route = $route['_route'];

        // go to the install page
        if ($route != 'install' && strpos($route, 'install_') !== 0) {
            $event->setResponse(new RedirectResponse($this->router->generate('install')));
        }
    }
}