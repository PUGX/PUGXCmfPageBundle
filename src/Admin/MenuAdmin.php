<?php
/**
 * Created by PhpStorm.
 * User: manuele
 * Date: 02/06/15
 * Time: 16:44
 */

namespace Webgriffe\Cmf\PageBundle\Admin;


use Sonata\AdminBundle\Route\RouteCollection;

class MenuAdmin extends \Symfony\Cmf\Bundle\MenuBundle\Admin\MenuAdmin
{
    public function hasRoute($name)
    {
        return false;
    }

}
