<?php

namespace Stfalcon\Bundle\TinymceBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;

class StfalconTinymceExtension extends Extension
{
    /**
     * Handles the knplabs_menu configuration.
     *
     * @param  array $configs The configurations being loaded
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
//        $configuration = new Configuration();
//        $processor = new Processor();
//        $config = $processor->process($configuration->getConfigTree(), $configs);

        $config = array();
        foreach($configs as $c) {
            $config = array_merge($config, $c);
        }

        $container->setParameter('stfalcon_tinymce.config', $config);
    }

    /**
     * @see Symfony\Component\DependencyInjection\Extension\ExtensionInterface
     */
    public function getAlias()
    {
        return 'stfalcon_tinymce';
    }
}
