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
        $defaults = $this->getTinymceDefaults();

        $treeBuilder = new TreeBuilder();

        return $treeBuilder
            ->root('stfalcon_tinymce', 'array')
            ->children()
            // Include jQuery (true) library or not (false)
            ->booleanNode('include_jquery')->defaultFalse()->end()
            // Use jQuery (true) or standalone (false) build of the TinyMCE
            ->booleanNode('tinymce_jquery')->defaultFalse()->end()
            // Set init to true to use callback on the event init
            ->booleanNode('use_callback_tinymce_init')->defaultFalse()->end()
            // Selector
            ->scalarNode('textarea_class')->end() // @deprecated
            ->scalarNode('selector')->end()
            // base url for content
            ->scalarNode('base_url')->end()
            // Default language for all instances of the editor
            ->scalarNode('language')->defaultNull()->end()
            ->arrayNode('theme')
            ->useAttributeAsKey('name')
            ->prototype('array')
            ->useAttributeAsKey('name')
            ->prototype('variable')->end()
            ->end()
            // Add default theme if it doesn't set
            ->defaultValue($defaults)
            ->end()
            // Configure custom TinyMCE buttons
            ->arrayNode('tinymce_buttons')
            ->useAttributeAsKey('name')
            ->prototype('array')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('title')->isRequired()->end()
            ->scalarNode('image')->defaultNull()->end()
            ->end()
            ->end()
            ->end()
            // Configure external TinyMCE plugins
            ->arrayNode('external_plugins')
            ->useAttributeAsKey('name')
            ->prototype('array')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('url')->isRequired()->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end();
    }

    /**
     * Get default configuration of the each instance of editor
     *
     * @return array
     */
    private function getTinymceDefaults()
    {
        return array(
            'advanced' => array(
                "theme"        => "modern",
                "plugins"      => array(
                    "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                    "searchreplace wordcount visualblocks visualchars code fullscreen",
                    "insertdatetime media nonbreaking save table contextmenu directionality",
                    "emoticons template paste textcolor"
                ),
                "toolbar1"     => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
                "toolbar2"     => "print preview media | forecolor backcolor emoticons",
                "image_advtab" => true,
            ),
            'simple'   => array()
        );
    }
}