<?php

namespace Stfalcon\Bundle\TinymceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration
{
    /**
     * Generates the configuration tree.
     *
     * @return \Symfony\Component\DependencyInjection\Configuration\NodeInterface
     */
    public function getConfigTree()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('stfalcon_tinymce', 'array');
        $rootNode
            ->children()
                // http://tinymce.moxiecode.com/wiki.php/Configuration:mode
                ->scalarNode('mode')->defaultValue('textareas')
                    ->validate()
                        ->ifNotInArray(array('textareas', 'specific_textareas', 'exact', 'none'))
                        ->thenInvalid('TynymceBundle: the %s mode is not supported')
                    ->end()
                ->end()
                // http://tinymce.moxiecode.com/wiki.php/Configuration:theme
                ->scalarNode('theme')->defaultValue('advanced')
                    ->validate()
                        ->ifNotInArray(array('advanced', 'simple'))
                        ->thenInvalid('TynymceBundle: the %s theme is not supported')
                    ->end()
                ->end()
                // http://tinymce.moxiecode.com/wiki.php/Configuration:Advanced_theme
                ->scalarNode('theme_advanced_buttons1')
                    ->defaultValue("mylistbox,mysplitbutton,bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright,justifyfull,bullist,numlist,undo,redo,link,unlink")
                ->end()
                ->scalarNode('theme_advanced_buttons2')->defaultValue("")->end()
                ->scalarNode('theme_advanced_buttons3')->defaultValue("")->end()
                // http://tinymce.moxiecode.com/wiki.php/Configuration:plugins
                ->scalarNode('plugins')->defaultValue("")->end()
                
            ->end();

        return $treeBuilder->buildTree();
    }
}