<?php
namespace Stfalcon\Bundle\TinymceBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Twig Extension for TinyMce support.
 *
 * @author  naydav <web@naydav.com>
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
     * Initialize tinymce  helper
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
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
     */
    public function tinymce_init()
    {
        //$assets = $this->getContainer()->get('templating.helper.assets');
        return ($this->getContainer()->get('templating')->render('StfalconTinymceBundle:Script:init.html.twig', array(
            'tinymce_config_json' => json_encode($this->getContainer()->getParameter('stfalcon_tinymce.config'))
        )));
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
