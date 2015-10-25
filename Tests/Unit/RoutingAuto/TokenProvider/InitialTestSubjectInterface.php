<?php
/**
 * Created by PhpStorm.
 * User: manuele
 * Date: 24/10/15
 * Time: 02:08
 */

namespace PUGX\Cmf\PageBundle\Tests\Unit\RoutingAuto\TokenProvider;


use PUGX\Cmf\PageBundle\RoutingAuto\TokenProvider\PrimaryMenuNodeProviderInterface;
use PUGX\Cmf\PageBundle\RoutingAuto\TokenProvider\RouteTokenProviderInterface;

interface InitialTestSubjectInterface extends PrimaryMenuNodeProviderInterface, RouteTokenProviderInterface
{
}
