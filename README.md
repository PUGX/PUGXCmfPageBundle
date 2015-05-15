Webgriffe CMF Page Bundle
=========================

Pages CMS system for Symfony2 built on top of Symfony CMF.

Compatibility
-------------

This bundle has been tested on the following dependencies already .

```
"php": ">=5.3.3",
"symfony/symfony": "~2.5",
"doctrine/doctrine-bundle": "~1.2",
"doctrine/data-fixtures": "~1.0",
"doctrine/doctrine-cache-bundle": "~1.0",
"twig/extensions": "~1.0",
"symfony/assetic-bundle": "~2.3",
"symfony/swiftmailer-bundle": "~2.3",
"symfony/monolog-bundle": "~2.4",
"sensio/distribution-bundle": "~3.0",
"sensio/framework-extra-bundle": "~3.0",
"incenteev/composer-parameter-handler": "~2.0",
"nelmio/alice": "~1.0",
```

Installation
------------

Install Symfony2 standard edition and then add the dependency to this bundle in your `composer.json`.

```
	// ...
	"require": {
		// ...
        "webgriffe/cmf-page-bundle": "dev-master"
    },
    // ...
```

You also have to add `downloadCreateAndCkeditor` composer script handler to your `composer.json`:

```
	// ...
    "scripts": {
        "post-install-cmd": [
        	// ...
        	"Symfony\\Cmf\\Bundle\\CreateBundle\\Composer\\ScriptHandler::downloadCreateAndCkeditor"
        ],
        "post-update-cmd": [
        	// ...
        	"Symfony\\Cmf\\Bundle\\CreateBundle\\Composer\\ScriptHandler::downloadCreateAndCkeditor",
        ]
    },
    // ...
```
And run `composer update webgriffe/cmf-page-bundle`.

Now you have to add Symfony CMF bundles and `WebgriffeCmfPageBundle` to your `AppKernel`:

```
    public function registerBundles()
    {
        $bundles = array(
        	// ...
        	// Symfony CMF Bundles
            new Doctrine\Bundle\PHPCRBundle\DoctrinePHPCRBundle(),
            new Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
            new Symfony\Cmf\Bundle\ContentBundle\CmfContentBundle(),
            new Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),
            new Symfony\Cmf\Bundle\RoutingAutoBundle\CmfRoutingAutoBundle(),

            new Symfony\Cmf\Bundle\BlockBundle\CmfBlockBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Sonata\CoreBundle\SonataCoreBundle(),

            new Symfony\Cmf\Bundle\MenuBundle\CmfMenuBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),

            new Symfony\Cmf\Bundle\CreateBundle\CmfCreateBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),

            new Webgriffe\Cmf\PageBundle\WebgriffeCmfPageBundle(),
            new Symfony\Cmf\Bundle\CoreBundle\CmfCoreBundle(),
        );
        
        // ...
```

Pay attention that `CmfCoreBundle` must be loaded after any bundle which prepend config (such as `WebgriffeCmfPageBundle`) because in `CmfCoreExtension::prepend()` it copies configuration for others CMF bundles. So because `WebgriffeCmfPageBundle` would prepend configuration for `cmf_core` it must be loaded before `CmfCoreBundle` to correctly copy such config on other CMF bundles.
         