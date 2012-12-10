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
                    // Textarea class
                    ->scalarNode('textarea_class')->end()
                    // base url for content
                    ->scalarNode('base_url')->end()
                    // Default language for all instances of the editor
                    ->scalarNode('language')->defaultNull()->end()
                    ->arrayNode('theme')
                        ->useAttributeAsKey('name')
                        ->prototype('array')
                            ->useAttributeAsKey('name')
                            ->beforeNormalization()
                                ->always()
                                ->then(function($array) use ($defaults) {
                                    // Merge default values with values from the config
                                    if (is_array($array)) {
                                        // Excepted values
                                        $unchangeableKeys = array('language');
                                        foreach ($array as $k => $v) {
                                            if (in_array($k, $unchangeableKeys)) {
                                                continue;
                                            }
                                            $defaults[$k] = $v;
                                        }
                                    }

                                    return $defaults;
                                })
                            ->end()
                            ->prototype('variable')->end()
                        ->end()
                        // Add default theme if it doesn't set
                        ->defaultValue(array('simple' => $defaults))
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
        // Default array set of the buttons
        $buttons = array(
            'fullscreen',
            'code',
            'separator',
            'bold',
            'italic',
            'underline',
            'separator',
            'strikethrough',
            'justifyleft',
            'justifycenter',
            'justifyright',
            'justifyfull',
            'bullist',
            'numlist',
            'undo',
            'redo',
            'link',
            'unlink'
        );

        return array(
            'mode'                              => 'textareas',
            'theme'                             => 'advanced',
            'theme_advanced_buttons1'           => implode(',', $buttons),
            'theme_advanced_toolbar_location'   => 'top',
            'theme_advanced_toolbar_align'      => 'left',
            'theme_advanced_statusbar_location' => 'bottom',
            'paste_auto_cleanup_on_paste'       =>  true,
            'plugins'                           => 'fullscreen'
        );
    }
}