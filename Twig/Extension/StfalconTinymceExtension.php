<?php
namespace Stfalcon\Bundle\TinymceBundle\Twig\Extension;

use Stfalcon\Bundle\TinymceBundle\Helper\LocaleHelper;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\RouterInterface;

/**
 * Twig Extension for TinyMce support.
 *
 * @author naydav <web@naydav.com>
 */
class StfalconTinymceExtension extends \Twig_Extension
{
    /**
     * @var ContainerInterface $container Container interface
     */
    protected $container;

    /**
     * Asset Base Url
     *
     * Used to over ride the asset base url (to not use CDN for instance)
     *
     * @var String
     */
    protected $baseUrl;
    /**
     * Trigger of initialization
     *
     * @var bool
     */
    private $initialized = false;

    /**
     * @var Packages
     */
    private $packages;

    /**
     * @param ContainerInterface $container
     * @param Packages           $packages
     */
    public function __construct(ContainerInterface $container, Packages $packages)
    {
        $this->container = $container;
        $this->packages = $packages;
    }

    /**
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
        return [
            'tinymce_init' => new \Twig_SimpleFunction(
                'tinymce_init',
                [$this, 'printIfNotInit'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    /**
     * Be smart - initialize things only once
     *
     * @param FormView $form
     * @param array $options
     * @return string|null
     */
    public function printIfNotInit($form, $options = array())
    {
        $html = '';
        if(!$this->initialized) {
            $this->initialized = true;
            $html .= $this->tinymceInit($form, $options);
        }

        return $html . $this->getService('templating')->render('StfalconTinymceBundle:Script:textarea.html.twig', ['form' => $form]);
    }

    /**
     * TinyMce initializations
     *
     * @param FormView $form
     * @param array $options
     *
     * @return string
     */
    public function tinymceInit($form, $options = []): string
    {
        $config = $this->getParameter('stfalcon_tinymce.config');
        $config = array_merge_recursive($config, $options);

        $this->baseUrl = (!isset($config['base_url']) ? null : $config['base_url']);

        // Asset package name
        $assetPackageName = (!isset($config['asset_package_name']) ? null : $config['asset_package_name']);
        unset($config['asset_package_name']);

        /** @var $assets \Symfony\Component\Templating\Helper\CoreAssetsHelper */
        $assets = $this->packages;
        $browsers = [];

        // set route of filebrowser
        if(isset($config['file_browser']) && $config['file_browser'] && $type = $this->getFilePickerType($config['file_browser']['engine'])) {
            $browsers[$type] = [
                'type' => $type,
                'name' => $type . ' with TinyMCE',
            ];
            if($config['file_browser']['route']) {
                $route = $this->getRouter()->generate(
                    $config['file_browser']['engine'],
                    $config['file_browser']['route_parameters'] ?: []
                );
            } else {
                $route = '';
            }
            $config['file_browser_callback'] = 'getBrowser(\'' . $route . '\', \'' . str_replace(
                '"',
                '',
                $config['file_browser']['name'] ?: $browsers[$type]['name']
                ) . '\')';
        }

        // set route of filepicker
        if(isset($config['file_picker']) && $config['file_picker'] && $type = $this->getFilePickerType($config['file_picker']['engine'])) {
            $browsers[$type] = [
                'type' => $type,
                'name' => $type . ' with TinyMCE',
            ];
            if($config['file_picker']['route']) {
                $route = $this->getRouter()->generate(
                    $config['file_picker']['engine'],
                    $config['file_picker']['route_parameters'] ?: []
                );
            } else {
                $route = '';
            }
            $config['file_picker_callback'] = 'getBrowser(\'' . $route . '\', \'' . str_replace(
                '"',
                '',
                $config['file_picker']['name'] ?: $browsers[$type]['name']
                ) . '\')';
        }

        // If the language is not set in the config...
        if (!isset($config['language']) || empty($config['language'])) {
            // get it from the request
            $config['language'] = $this->container->get('request')->getLocale();
        }

        $config['language'] = LocaleHelper::getLanguage($config['language']);

        $langDirectory = __DIR__.'/../../Resources/public/vendor/tinymce/langs/';

        // A language code coming from the locale may not match an existing language file
        if (!file_exists($langDirectory . $config['language'].'.js')) {
            unset($config['language']);
        }

        if (isset($config['language']) && $config['language']) {
            // TinyMCE does not allow to set different languages to each instance
            foreach ($config['theme'] as $themeName => $themeOptions) {
                $config['theme'][$themeName]['language'] = $config['language'];
            }
        }

        if(!isset($config['theme']['simple'])) {
            $config['theme']['simple'] = [
                'language' => $config['language']
            ];
        }

        if(!isset($config['theme']['advanced'])) {
            $config['theme']['advanced'] = [
                'language' => $config['language']
            ];
        }

        if (isset($config['theme']) && $config['theme']) {
            // Parse the content_css of each theme so we can use 'asset[path/to/asset]' in there
            foreach ($config['theme'] as $themeName => $themeOptions) {
                if (isset($themeOptions['content_css'])) {
                    // As there may be multiple CSS Files specified we need to parse each of them individually
                    $cssFiles = $themeOptions['content_css'];
                    if (!\is_array($themeOptions['content_css'])) {
                        $cssFiles = explode(',', $themeOptions['content_css']);
                    }

                    foreach ($cssFiles as $idx => $file) {
                        $cssFiles[$idx] = $this->getAssetsUrl(trim($file)); // we trim to be sure we get the file without spaces.
                    }

                    // After parsing we add them together again.
                    $config['theme'][$themeName]['content_css'] = implode(',', $cssFiles);
                }
            }
        }

        $tinymceConfiguration = \preg_replace(
            [
                '/"file_browser_callback":"([^"]+)"\s*/',
                '/"file_picker_callback":"([^"]+)"\s*/',
                '/"paste_preprocess":"([^"]+)"\s*/',
            ],
            [
                'file_browser_callback:$1',
                'file_picker_callback:$1',
                'paste_preprocess:$1',
            ],
            \json_encode($config)
        );

        return $this->getService('twig')->render(
            '@StfalconTinymce/Script/init.html.twig',
            [
                'tinymce_config' => $tinymceConfiguration,
                'include_jquery' => $config['include_jquery'],
                'tinymce_jquery' => $config['tinymce_jquery'],
                'asset_package_name' => $assetPackageName,
                'base_url' => $this->baseUrl,
                'form'               => $form,
                'file_browsers'      => $browsers,
            ]
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName(): string
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
    protected function getAssetsUrl(string $inputUrl): string
    {
        $assets = $this->packages;

        $url = preg_replace('/^asset\[(.+)\]$/i', '$1', $inputUrl);

        if ($inputUrl !== $url) {
            return $assets->getUrl($this->baseUrl.$url);
        }

        return $inputUrl;
    }

    /**
     * @param $filePicker
     * @return string
     */
    protected function getFilePickerType($filePicker)
    {
        switch ($filePicker) {
            case 'elfinder':
                return 'elfinder';
            default:
                return '';
        }
    }

    /**
     * @return RouterInterface
     */
    private function getRouter()
    {
        return $this->container->get('router');
    }
}
