<?php
/**
 * Created by PhpStorm.
 * User: manuele
 * Date: 15/10/15
 * Time: 16:41
 */

namespace PUGX\Cmf\PageBundle\Routing;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Cmf\Component\Routing\RedirectRouteInterface;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Cmf\Component\RoutingAuto\Model\AutoRouteInterface;
use Symfony\Component\Routing\Route;

trait RouteReferrersTrait
{
    /**
     * @var RouteObjectInterface[]
     * @PHPCR\Referrers(referringDocument="Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr\Route", referencedBy="content")
     */
    protected $routes;

    /**
     * Add a route to the collection.
     *
     * @param Route $route
     */
    public function addRoute($route)
    {
        if (!$this->routes) {
            $this->routes = new ArrayCollection();
        }
        $this->routes->add($route);
    }

    /**
     * Remove a route from the collection.
     *
     * @param Route $route
     */
    public function removeRoute($route)
    {
        if (!$this->routes) {
            $this->routes = new ArrayCollection();
        }
        $this->routes->removeElement($route);
    }

    /**
     * Get the routes that point to this content.
     * When a redirect route is added, it's the first route in the collection of routes, and that route is the one
     * used to generate URLs. So the URL displayed will always result in a redirection, instead of the direct
     * access. Per suggestion on the open ticket, I am sorting the routes so that the direct route is first, so
     * the route generated will be direct.
     *
     * https://github.com/symfony-cmf/RoutingAuto/issues/31#issuecomment-73904458
     *
     * @return Route[] Route instances that point to this content
     */
    public function getRoutes()
    {
        if (!$this->routes) {
            $this->routes = new ArrayCollection();
        }
        /**
         * Routes are not auto routes and they don't redirect.
         * Auto routes are auto routes that don't redirect
         * Redirects are any route that redirects.
         */
        $buckets = array('routes' => array(), 'autoRoutes' => array(), 'redirectRoutes' => array());

        //## Sort routes into their correct bucket.
        foreach ($this->routes as $route) {
            if ($route instanceof AutoRouteInterface) {
                if ($route->getRedirectTarget()) {
                    $buckets['redirectRoutes'][] = $route;
                } else {
                    $buckets['autoRoutes'][] = $route;
                }
            } elseif ($route instanceof RedirectRouteInterface) {
                $buckets['redirectRoutes'][] = $route;
            } else {
                $buckets['routes'][] = $route;
            }
        }

        //## Flatten the buckets into one array, routes, auto, then redirect
        $orderedRoutes = array();
        array_walk_recursive(
            $buckets,
            function ($route) use (&$orderedRoutes) {
                $orderedRoutes[] = $route;
            }
        );

        return new ArrayCollection($orderedRoutes);
    }

    /**
     * Get the route key.
     *
     * This key will be used as route name instead of the symfony core compatible
     * route name and can contain any characters.
     *
     * Return null if you want to use the default key.
     *
     * @return string the route name
     */
    public function getRouteKey()
    {
        return null;
    }

    /**
     * Get the content document this route entry stands for. If non-null,
     * the ControllerClassMapper uses it to identify a controller and
     * the content is passed to the controller.
     *
     * If there is no specific content for this url (i.e. its an "application"
     * page), may return null.
     *
     * @return object the document or entity this route entry points to
     */
    public function getContent()
    {
        return $this;
    }

    public function getRoutesWithoutRedirectRoutes()
    {
        $routes = array();
        foreach ($this->getRoutes() as $route) {
            if (($route instanceof AutoRouteInterface && !$route->getRedirectTarget()) &&
                !$route instanceof RedirectRouteInterface
            ) {
                $routes[] = $route;
            }
        }
        return $routes;
    }
}
