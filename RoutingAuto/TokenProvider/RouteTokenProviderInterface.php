<?php
/**
 * Created by PhpStorm.
 * User: manuele
 * Date: 24/10/15
 * Time: 01:59
 */

namespace PUGX\Cmf\PageBundle\RoutingAuto\TokenProvider;


interface RouteTokenProviderInterface
{
    /**
     * Returns the string used by MenuPathTokenProvider to build the route path. It will be slugged automatically.
     *
     * @return string
     */
    public function provideRouteToken();
}
