<?php

namespace PUGX\Cmf\PageBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('pugx_cmf_page');

        $info = 'Define a list of menus to be created for your site; ' .
            'for example { main: "Main Menu", footer: "Footer Menu" }.';
        $rootNode
            ->children()
                ->arrayNode('menu')
                ->info($info)
                ->isRequired()
                ->useAttributeAsKey('name')
                ->prototype('scalar')
            ->end()
        ;

        return $treeBuilder;
    }
}
