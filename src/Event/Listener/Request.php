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
     * Application is installed
     *
     * @var boolean
     */
    protected $installed = false;

    /**
     * Router
     *
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    protected $router;

    /**
     * Construct
     *
     * @param boolean $installed
     * @param \Symfony\Bundle\FrameworkBundle\Routing\Router $router
     */
    public function __construct($installed, Router $router) {
        $this->installed = $installed;
        $this->router = $router;
    }

    /**
     * Kernel request handler
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($this->installed || $event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        $url = $this->router->generate('install');
        if (strpos($event->getRequest()->getRequestUri(), $url) !== 0) {
            $event->setResponse(new RedirectResponse($url));
        }
    }
}
