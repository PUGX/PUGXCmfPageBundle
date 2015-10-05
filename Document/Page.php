<?php

/**
 * Created by PhpStorm.
 * User: manuele
 * Date: 23/05/15
 * Time: 18:15.
 */

namespace PUGX\Cmf\PageBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\PHPCR\HierarchyInterface;
use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCR;
use Knp\Menu\NodeInterface;
use Symfony\Cmf\Bundle\MenuBundle\Model\MenuNodeReferrersInterface;
use Symfony\Cmf\Component\Routing\RedirectRouteInterface;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Cmf\Component\Routing\RouteReferrersInterface;
use Symfony\Cmf\Component\RoutingAuto\Model\AutoRouteInterface;
use Symfony\Component\Routing\Route;

/**
 * @PHPCR\Document(referenceable=true)
 */
class Page implements HierarchyInterface, RouteReferrersInterface, MenuNodeReferrersInterface, RedirectRouteInterface
{
    /**
     * @PHPCR\Id()
     */
    protected $id;

    /**
     * @PHPCR\Nodename()
     */
    protected $name;

    /**
     * @PHPCR\ParentDocument()
     */
    protected $parentDocument;

    /**
     * @var RouteObjectInterface[]
     * @PHPCR\Referrers(referringDocument="Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr\Route", referencedBy="content")
     */
    protected $routes;

    /**
     * @var NodeInterface[]
     * @PHPCR\Referrers(referringDocument="PUGX\Cmf\PageBundle\Document\MenuNode", referencedBy="content", cascade={"persist", "remove"})
     */
    protected $menuNodes;

    /**
     * @PHPCR\String()
     */
    protected $title;

    /**
     * @PHPCR\String()
     */
    protected $text;

    public function __construct()
    {
        $this->routes = new ArrayCollection();
        $this->menuNodes = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getParentDocument()
    {
        return $this->parentDocument;
    }

    /**
     * @param mixed $parentDocument
     *
     * @return $this|void
     */
    public function setParentDocument($parentDocument)
    {
        $this->parentDocument = $parentDocument;
    }

    /**
     * Get the parent document.
     *
     * @deprecated in favor of getParentDocument to avoid clashes with domain model parents.
     *
     * @return object|null
     */
    public function getParent()
    {
        return $this->getParentDocument();
    }

    /**
     * Set the parent document.
     *
     * @deprecated in favor of getParentDocument to avoid clashes with domain model parents.
     *
     * @param object $parent
     *
     * @return $this
     */
    public function setParent($parent)
    {
        $this->setParentDocument($parent);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

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
        $orderedRoutes = [];
        array_walk_recursive(
            $buckets,
            function ($route) use (&$orderedRoutes) {
                $orderedRoutes[] = $route;
            }
        );

        return $orderedRoutes;
    }

    /**
     * Get all menu nodes that point to this content.
     *
     * @return NodeInterface[] Menu nodes that point to this content
     */
    public function getMenuNodes()
    {
        return $this->menuNodes;
    }

    /**
     * Add a menu node for this content.
     *
     * @param NodeInterface $menu
     */
    public function addMenuNode(NodeInterface $menu)
    {
        if (!$this->menuNodes) {
            $this->menuNodes = new ArrayCollection();
        }
        $this->menuNodes->add($menu);
    }

    /**
     * Remove a menu node for this content.
     *
     * @param NodeInterface $menu
     */
    public function removeMenuNode(NodeInterface $menu)
    {
        $this->menuNodes->removeElement($menu);
    }

    /**
     * Get the absolute uri to redirect to external domains.
     *
     * If this is non-empty, the other methods won't be used.
     *
     * @return string target absolute uri
     */
    public function getUri()
    {
        return '';
    }

    /**
     * Get the target route document this route redirects to.
     *
     * If non-null, it is added as route into the parameters, which will lead
     * to have the generate call issued by the RedirectController to have
     * the target route in the parameters.
     *
     * @return RouteObjectInterface the route this redirection points to
     */
    public function getRouteTarget()
    {
        return $this->routes->first();
    }

    /**
     * Get the name of the target route for working with the symfony standard
     * router.
     *
     * @return string target route name
     */
    public function getRouteName()
    {
        return $this->routes->first()->getName();
    }

    /**
     * Whether this should be a permanent or temporary redirect
     *
     * @return boolean
     */
    public function isPermanent()
    {
        return true;
    }

    /**
     * Get the parameters for the target route router::generate()
     *
     * Note that for the DynamicRouter, you return the target route
     * document as field 'route' of the hashmap.
     *
     * @return array Information to build the route
     */
    public function getParameters()
    {
        return array();
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
}
