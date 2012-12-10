<?php
namespace Stfalcon\Bundle\TinymceBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Twig Extension for TinyMce support.
 *
 * @author naydav <web@naydav.com>
 */
class StfalconTinymceExtension extends \Twig_Extension
{
    /**
     * Container
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Initialize tinymce helper
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Gets a service.
     *
     * @param string $id The service identifier
     *
     * @return object The associated service
     */
    public function getService($id)
    {
        return $this->container->get($id);
    }

    /**
     * Get parameters from the service container
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getParameter($name)
    {
        return $this->container->getParameter($name);
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            'tinymce_init' => new \Twig_Function_Method($this, 'tinymce_init', array('is_safe' => array('html')))
        );
    }

    /**
     * TinyMce initializations
     *
     * @return string
     */
    public function tinymce_init()
    {

        $config  = $this->getParameter('stfalcon_tinymce.config');
        $baseURL = (!isset($config['base_url']) ? null : $config['base_url']);

        /** @var $assets \Symfony\Component\Templating\Helper\CoreAssetsHelper */
        $assets = $this->getService('templating.helper.assets');

        // Get path to tinymce script for the jQuery version of the editor
        $config['jquery_script_url'] = $assets->getUrl($baseURL . 'bundles/stfalcontinymce/vendor/tiny_mce/tiny_mce.jquery.js');

        // Get local button's image
        foreach ($config['tinymce_buttons'] as &$customButton) {
            $customButton['image'] = $this->getAssetsUrl($customButton['image']);
        }

        // Update URL to external plugins
        foreach ($config['external_plugins'] as &$extPlugin) {
            $extPlugin['url'] = $this->getAssetsUrl($extPlugin['url']);
        }

        // If the language is not set in the config...
        if (!isset($config['language']) || empty($config['language'])) {
            // get it from the request
            $config['language'] = $this->getService('request')->getLocale();
        }

        // Check the language code and trim it to 2 symbols (en_US to en, ru_RU to ru, ...)
        if (strlen($config['language']) > 2) {
            $config['language'] = substr($config['language'], 0, 2);
        }

        // TinyMCE does not allow to set different languages to each instance
        foreach ($config['theme'] as $themeName => $themeOptions) {
            $config['theme'][$themeName]['language'] = $config['language'];
        }

        return $this->getService('templating')->render('StfalconTinymceBundle:Script:init.html.twig', array(
            'tinymce_config' => json_encode($config),
            'include_jquery' => $config['include_jquery'],
            'tinymce_jquery' => $config['tinymce_jquery'],
            'base_url'       => $baseURL
        ));
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'stfalcon_tinymce';
    }


    /**
     * Get url from config string
     *
     * @param string $inputUrl
     *
     * @return string
     */
    protected function getAssetsUrl($inputUrl)
    {
        /** @var $assets \Symfony\Component\Templating\Helper\CoreAssetsHelper */
        $assets = $this->getService('templating.helper.assets');

        $url = preg_replace('/^asset\[(.+)\]$/i', '$1', $inputUrl);

            if ($inputUrl !== $url) {
            return $assets->getUrl($url);
        }

        return $inputUrl;
    }
}