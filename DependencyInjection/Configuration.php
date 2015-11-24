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

        $rootNode
            ->children()
                ->scalarNode('title')
                    ->info('Define a title for the website (e.g. "My Awesome Site").')
                    ->isRequired()
                ->end()
                ->scalarNode('description')
                    ->info(
                        'Define a description for the website (e.g. "My Awesome Site SEO description."). ' .
                        'Will be used as default meta description.'
                    )
                    ->isRequired()
                ->end()
                ->scalarNode('keywords')
                    ->info(
                        'Define a list of comma separated keywords for the website (e.g. "awesome, pugx, cmf"). ' .
                        'Will be used as default meta keywords.'
                    )
                    ->isRequired()
                ->end()
                ->scalarNode('admin_logo')
                    ->info(
                        'Define the website logo path relative to web document root ' .
                        '(e.g. "bundles/pugxcmfpage/logo.png"). Will be used in the administrative interface.'
                    )
                    ->isRequired()
                ->end()
                ->arrayNode('menu')
                    ->info(
                        'Define a list of menus to be created for your site; ' .
                        'for example { main: "Main Menu", footer: "Footer Menu" }.'
                    )
                    ->isRequired()
                    ->useAttributeAsKey('name')
                    ->prototype('scalar')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
