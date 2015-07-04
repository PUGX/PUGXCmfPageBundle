<?php

use Symfony\Cmf\Component\Testing\HttpKernel\TestKernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends TestKernel
{
    /**
     * Use this method to declare which bundles are required
     * by the Kernel, e.g.
     *
     *    $this->requireBundleSets('default', 'phpcr_odm');
     *    $this->addBundle(new MyBundle);
     *    $this->addBundles(array(new Bundle1, new Bundle2));
     *
     */
    protected function configure()
    {
        $this->addBundles(
            array(
                new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
                new Symfony\Bundle\SecurityBundle\SecurityBundle(),
                new Symfony\Bundle\TwigBundle\TwigBundle(),
                new Symfony\Bundle\MonologBundle\MonologBundle(),
                new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
                new Symfony\Bundle\AsseticBundle\AsseticBundle(),
                new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
                new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

                // Symfony CMF Bundles
                new Doctrine\Bundle\PHPCRBundle\DoctrinePHPCRBundle(),
                new Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
                new Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),
                new Symfony\Cmf\Bundle\RoutingAutoBundle\CmfRoutingAutoBundle(),

                new Symfony\Cmf\Bundle\BlockBundle\CmfBlockBundle(),
                new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
                new Symfony\Cmf\Bundle\TreeBrowserBundle\CmfTreeBrowserBundle(),
                new Sonata\BlockBundle\SonataBlockBundle(),
                new Sonata\CoreBundle\SonataCoreBundle(),
                new Sonata\jQueryBundle\SonatajQueryBundle(),
                new Sonata\AdminBundle\SonataAdminBundle(),
                new Sonata\DoctrinePHPCRAdminBundle\SonataDoctrinePHPCRAdminBundle(),

                new Symfony\Cmf\Bundle\MenuBundle\CmfMenuBundle(),
                new Knp\Bundle\MenuBundle\KnpMenuBundle(),

                new Symfony\Cmf\Bundle\CreateBundle\CmfCreateBundle(),
                new FOS\RestBundle\FOSRestBundle(),
                new JMS\SerializerBundle\JMSSerializerBundle(),

                new PUGX\Cmf\PageBundle\PUGXCmfPageBundle(),
                new Symfony\Cmf\Bundle\CoreBundle\CmfCoreBundle(),
            )
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
