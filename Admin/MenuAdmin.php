<?php

/**
 * Created by PhpStorm.
 * User: manuele
 * Date: 02/06/15
 * Time: 16:44.
 */

namespace PUGX\Cmf\PageBundle\Admin;

use Sonata\AdminBundle\Route\RouteCollection;

class MenuAdmin extends \Symfony\Cmf\Bundle\MenuBundle\Admin\MenuAdmin
{
    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clear();
    }
}
