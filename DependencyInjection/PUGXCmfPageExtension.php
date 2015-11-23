<?php

namespace PUGX\Cmf\PageBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Parser;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class PUGXCmfPageExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter($this->getAlias() . '.title', $config['title']);
        $container->setParameter($this->getAlias() . '.description', $config['description']);
        $container->setParameter($this->getAlias() . '.keywords', $config['keywords']);
        $container->setParameter($this->getAlias() . '.admin_logo', $config['admin_logo']);
        $container->setParameter($this->getAlias() . '.menu', $config['menu']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);
        $container->setParameter($this->getAlias() . '.title', $config['title']);
        $container->setParameter($this->getAlias() . '.description', $config['description']);
        $container->setParameter($this->getAlias() . '.keywords', $config['keywords']);
        $container->setParameter($this->getAlias() . '.admin_logo', $config['admin_logo']);

        $configs = $this->loadYmlConfig('prepended_config.yml');
        foreach ($configs as $name => $config) {
            $container->prependExtensionConfig($name, $config);
        }
    }

    protected function loadYmlConfig($file)
    {
        $fileLocator = new FileLocator(__DIR__.'/../Resources/config');
        $file = $fileLocator->locate($file);

        if (!stream_is_local($file)) {
            throw new InvalidArgumentException(sprintf('This is not a local file "%s".', $file));
        }

        if (!file_exists($file)) {
            throw new InvalidArgumentException(sprintf('The service file "%s" is not valid.', $file));
        }

        $ymlParser = new Parser();

        return $ymlParser->parse(file_get_contents($file));
    }
}
