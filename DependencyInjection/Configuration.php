<?php

namespace OpenWide\AgendaBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('open_wide_agenda');

        $rootNode
            ->children()
                ->arrayNode('root')
                    ->children()
                        ->integerNode('location_id')->min(0)->defaultValue(2)->end()
                    ->end()
                ->end()
                ->arrayNode('template')
                    ->children()
                        ->scalarNode('index')->defaultValue('OpenWideAgendaBundle::index.html.twig')->end()
                        ->scalarNode('indexmini')->defaultValue('OpenWideAgendaBundle::index_mini.html.twig')->end()
                    ->end()
                ->end()
                ->arrayNode('controller')
                    ->children()
                        ->scalarNode('agendaview')->defaultValue('OpenWide\AgendaBundle\Controller\AgendaViewController')->end()
                        ->scalarNode('eventview')->defaultValue('OpenWide\AgendaBundle\Controller\EventViewController')->end()
                    ->end()
                ->end()
                ->arrayNode('helpers')
                    ->children()
                        ->scalarNode('agenda_fetch_by_legacy')->defaultValue('OpenWide\AgendaBundle\Helper\FetchByLegacy')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
