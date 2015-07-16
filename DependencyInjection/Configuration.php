<?php

namespace OpenWide\Bundle\PublishAgendaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('open_wide_publish_agenda');

        $rootNode
            ->children()
                ->arrayNode('event_folder')
                    ->children()
                        ->integerNode('location_id')->min(0)->defaultValue(2)->end()
                    ->end()
                ->end()
                ->arrayNode('paginate')
                    ->children()
                        ->integerNode('max_per_page')->min(1)->defaultValue(10)->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
