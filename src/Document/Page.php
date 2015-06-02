<?php
/**
 * Created by PhpStorm.
 * User: manuele
 * Date: 23/05/15
 * Time: 18:15
 */

namespace Webgriffe\Cmf\PageBundle\Document;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\PHPCR\HierarchyInterface;
use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCR;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Cmf\Component\Routing\RouteReferrersInterface;
use Symfony\Component\Routing\Route;

/**
 * @PHPCR\Document(referenceable=true)
 */
class Page implements HierarchyInterface, RouteReferrersInterface
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
     * @PHPCR\Parentdocument()
     */
    protected $parentDocument;

    /**
     * @var RouteObjectInterface[]
     */
    protected $routes;

    /**
     * @PHPCR\String()
     */
    protected $title;

    /**
     * @PHPCR\String()
     */
    protected $content;

    public function __construct()
    {
        $this->routes = new ArrayCollection();
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
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
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
     *
     * @return Route[] Route instances that point to this content
     */
    public function getRoutes()
    {
        return $this->routes;
    }
}
