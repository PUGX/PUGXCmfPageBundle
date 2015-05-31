<?php
/**
 * Created by PhpStorm.
 * User: manuele
 * Date: 31/05/15
 * Time: 17:38
 */

namespace Webgriffe\Cmf\PageBundle\EventListener;


use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ODM\PHPCR\DocumentManager;
use Symfony\Cmf\Component\Routing\RouteReferrersInterface;

class ContentRoutesLoader
{
    public function postLoad(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        $objectManager = $args->getObjectManager();

        if ($object instanceof RouteReferrersInterface && $objectManager instanceof DocumentManager) {
            $routes = $objectManager->getReferrers($object, null, null, null, 'Symfony\Component\Routing\Route');
            foreach ($routes as $route) {
                $object->addRoute($route);
            }
        }
    }
}
