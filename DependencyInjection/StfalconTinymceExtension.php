<?php

namespace Stfalcon\Bundle\TinymceBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * StfalconTinymceExtension.
 */
class StfalconTinymceExtension extends Extension
{
    /**
     * Loads the StfalconTinymceBundle configuration.
     *
     * @param array            $configs   An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        // Get default configuration of the bundle
        $config = $this->processConfiguration(new Configuration(), $configs);

        if (empty($config['selector'])) {
            $config['selector'] = ['.tinymce'];
        }
        if (empty($config['theme'])) {
            $config['theme'] = [
                'simple' => [],
            ];
        } else {
            foreach ($config['theme'] as &$bundleTheme) {
                // Quick fix for the removed obsolete themes
                if (isset($bundleTheme['theme']) && \in_array($bundleTheme['theme'], ['advanced', 'simple'], true)) {
                    $bundleTheme['theme'] = 'modern';
                }
                unset($bundleTheme);
            }
        }

        $container->setParameter('stfalcon_tinymce.config', $config);

        // load dependency injection config
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('service.xml');
    }

    /**
     * Returns the recommended alias to use in XML.
     *
     * This alias is also the mandatory prefix to use when using YAML.
     *
     * @return string The alias
     */
    public function getAlias(): string
    {
        return 'stfalcon_tinymce';
    }
}
