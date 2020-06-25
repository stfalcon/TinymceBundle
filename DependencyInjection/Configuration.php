<?php

namespace Stfalcon\Bundle\TinymceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * TinymceBundle configuration structure.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        return $treeBuilder
            ->root('tinymce', 'array')
                ->children()
                    // Default language for all instances of the editor
                    ->scalarNode('language')->defaultNull()->end()
                    // Selector
                    ->scalarNode('selector')->defaultValue('[data-tinymce]')->end()
                    // Set init to true to use callback on the event init
                    ->booleanNode('use_callback_tinymce_init')->defaultFalse()->end()
                    // base url for content
                    ->scalarNode('base_url')->end()
                    // asset packageName
                    ->scalarNode('asset_package_name')->end()
                    // plugins
                    ->scalarNode('plugins')->defaultValue('')->end()
                    // toolbar
                    ->scalarNode('toolbar')->defaultValue('undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent')->end()
                    ->scalarNode('quickbars_selection_toolbar')->defaultValue('')->end()
                    ->arrayNode('file_picker')
                        ->children()
                            ->scalarNode('engine')->defaultNull()->end()
                            ->scalarNode('name')->defaultNull()->end()
                            ->scalarNode('route')->defaultNull()->end()
                            ->arrayNode('route_parameters')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('file_browser')
                        ->children()
                            ->scalarNode('engine')->defaultNull()->end()
                            ->scalarNode('name')->defaultNull()->end()
                            ->scalarNode('route')->defaultNull()->end()
                            ->arrayNode('route_parameters')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
