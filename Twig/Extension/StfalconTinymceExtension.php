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
     * @param array $options
     *
     * @return string
     */
    public function tinymce_init($options = array())
    {

        $config = $this->getParameter('stfalcon_tinymce.config');

        /** @var $assets \Symfony\Component\Templating\Helper\CoreAssetsHelper */
        $assets = $this->getService('templating.helper.assets');

        // Get path to tinymce script for the jQuery version of the editor
        $config['jquery_script_url'] = $assets->getUrl('bundles/stfalcontinymce/vendor/tiny_mce/tiny_mce.js');;

        //      tinymce_buttons:
        //        my_custom_button: 
        //          title: 'insert this thing'
        //          image: "asset[bundles/mybundle/images/myicon.ico]"
        foreach ($config['tinymce_buttons'] as &$customButton)
        {
            $imageUrl = $customButton['image'];
            $url = preg_replace('/^asset\[(.+)\]$/i', '$1', $imageUrl);
            if ($imageUrl !== $url)
            {
                $customButton['image'] = $assets->getUrl($url);                
            }
        }
        
        return $this->getService('templating')->render('StfalconTinymceBundle:Script:init.html.twig', array(
            'tinymce_config' => json_encode($config),
            'include_jquery' => $config['include_jquery'],
            'tinymce_jquery' => $config['tinymce_jquery']
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
}
