<?php

/**
 * Created by PhpStorm.
 * User: manuele
 * Date: 02/06/15
 * Time: 16:44.
 */

namespace PUGX\Cmf\PageBundle\Admin;

class MenuAdmin extends \Symfony\Cmf\Bundle\MenuBundle\Admin\MenuAdmin
{
    public function hasRoute($name)
    {
        return false;
    }
}
