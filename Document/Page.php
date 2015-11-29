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
use PUGX\Cmf\PageBundle\Routing\RouteReferrersRedirectToFirstRouteTrait;
use PUGX\Cmf\PageBundle\RoutingAuto\TokenProvider\PrimaryMenuNodeProviderInterface;
use PUGX\Cmf\PageBundle\RoutingAuto\TokenProvider\RouteTokenProviderInterface;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode as PHPCRMenuNode;
use Symfony\Cmf\Bundle\MenuBundle\Model\MenuNodeReferrersInterface;
use Symfony\Cmf\Bundle\SeoBundle\SeoAwareInterface;
use Symfony\Cmf\Bundle\SeoBundle\SeoAwareTrait;
use Symfony\Cmf\Component\Routing\RedirectRouteInterface;
use Symfony\Cmf\Component\Routing\RouteReferrersInterface;

/**
 * @PHPCR\Document(referenceable=true, repositoryClass="PUGX\Cmf\PageBundle\Document\Repository\PageRepository")
 */
class Page implements
    RouteReferrersInterface,
    MenuNodeReferrersInterface,
    RedirectRouteInterface,
    RouteTokenProviderInterface,
    PrimaryMenuNodeProviderInterface,
    SeoAwareInterface
{
    use RouteReferrersRedirectToFirstRouteTrait;
    use SeoAwareTrait;

    /**
     * @PHPCR\Uuid
     **/
    protected $uuid;

    /**
     * @PHPCR\Id(strategy="repository")
     */
    protected $id;

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
        $this->menuNodes = new ArrayCollection();
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
    public function getUuid()
    {
        return $this->uuid;
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
     * The __toString method allows a class to decide how it will react when it is converted to a string.
     *
     * @return string
     * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
     */
    public function __toString()
    {
        return (string)($this->getTitle());
    }

    /**
     * Returns MenuNode on which the MenuPathTokenProvider builds the route path.
     *
     * @return PHPCRMenuNode
     */
    public function providePrimaryMenuNode()
    {
        return $this->menuNodes->first();
    }

    /**
     * Returns the string used by MenuPathTokenProvider to build the route path. It will be slugged automatically.
     *
     * @return string
     */
    public function provideRouteToken()
    {
        return $this->getTitle();
    }
}
