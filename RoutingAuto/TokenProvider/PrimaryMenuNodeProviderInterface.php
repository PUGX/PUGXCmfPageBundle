<?php
/**
 * Created by PhpStorm.
 * User: manuele
 * Date: 24/10/15
 * Time: 01:37
 */

namespace PUGX\Cmf\PageBundle\RoutingAuto\TokenProvider;



use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode;

interface PrimaryMenuNodeProviderInterface
{
    /**
     * Returns MenuNode on which the MenuPathTokenProvider builds the route path.
     *
     * @return MenuNode
     */
    public function providePrimaryMenuNode();
}
