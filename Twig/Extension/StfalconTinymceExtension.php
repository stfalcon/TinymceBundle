<?php
namespace Stfalcon\Bundle\TinymceBundle\Twig\Extension;

use Stfalcon\Bundle\TinymceBundle\Helper\LocaleHelper;
use Symfony\Component\Asset\Packages;
use Stfalcon\Bundle\TinymceBundle\Model\ConfigManager;
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
     * @var ConfigManager
     */
    private $configManager;

    /**
     * @var Packages
     */
    private $packages;

    /**
     * Initialize tinymce helper
     *
     * @param ContainerInterface $container
     * @param ConfigManager $configManager
     */
    public function __construct(ContainerInterface $container, ConfigManager $configManager, Packages $packages)
    {
        $this->container = $container;
        $this->configManager = $configManager;
        $this->packages = $packages;
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
        return [
            'tinymce_init' => new \Twig_SimpleFunction(
                'tinymce_init',
                [$this, 'printIfNotInit'],
                ['is_safe' => ['html']]
            ),
            'tinymce_simple' => new \Twig_SimpleFunction(
                'tinymce_simple',
                [$this, 'initSimple'],
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
    public function tinymceInit($form, $options = array()): string
    {
        $config = $this->getParameter('stfalcon_tinymce.config');
        $config = array_merge_recursive($config, $options);

        if(!isset($config['variables'])) {
            $config['variables'] = [];
        }

        $this->baseUrl = (!isset($config['base_url']) ? null : $config['base_url']);
        if($this->configManager->hasConfig('config')) {
            foreach ($this->configManager->getConfig('config') as $key => $value) {
                if(
                    isset($config[$key])
                    &&
                    (
                        is_array($config[$key])
                        && $value
                        || in_array(strtolower(gettype($config[$key])), ['string', 'integer', 'float', 'double', 'boolean'])
                        && $value !== null
                    )
                ) {
                    $config[$key] = is_array($config[$key]) ? array_merge($config[$key], $value) : $value;
                }
            }
        }

        if(isset($config['variables'], $config['variables']['list'], $config['variables']['title']) && $config['variables']) {
            $config['variable_mapper'] = $config['variables']['list'];
            if(!isset($config['variables']['tag'])) {
                $config['variables']['tag'] = 'span';
            }
            $config['setup'] = 'ed => { ' .
                'ed.ui.registry.addMenuButton(\'variables\', {' .
                        'text: \'' . str_replace(['""', "''"], '', $config['variables']['title']) . '\',' .
                        'classes: \'tinymce_erpbox_var\',' .
                        'fetch: callback => {' .
                            'let items = [];' .
                            'for (const key in ed.settings.variable_mapper) {' .
                                'items.push({' .
                                    'type: \'menuitem\',' .
                                    'text: ed.settings.variable_mapper[key],' .
                                    'onAction: () => {' .
                                        'ed.plugins.variable.addVariable(key);' .
                                    '}' .
                                '});' .
                            '}' .
                            'callback(items);' .
                        '}' .
                    '});' .
            '}';
            $config['variable_tag'] = $config['variables']['tag'];
            unset($config['variables']);
            $config['valid_elements'] .= ',*[]';
        }

        // Asset package name
        $assetPackageName = (!isset($config['asset_package_name']) ? null : $config['asset_package_name']);
        unset($config['asset_package_name']);
        $browsers = $this->getFileBrowsers($config);

        // overwrite config
        if($config['config_name'] !== 'default' && isset($config['theme'][$config['config_name']])) {
            $config = array_merge($config, $config['theme'][$config['config_name']]);
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

        return $this->getService('twig')->render(
            '@StfalconTinymce/Script/init.html.twig',
            [
                'tinymce_config'     => $this->getTinyMCEConfiguration($config),
                'asset_package_name' => $assetPackageName,
                'base_url'           => $this->baseUrl,
                'form'               => $form,
                'file_browsers'      => $browsers,
                'initializeFunction' => false,
            ]
        );
    }

    /**
     * Initialize TinyMCE in simple mode
     * @param $config
     * @param null $initializeFunctionName
     * @return string
     */
    public function initSimple($config, $initializeFunctionName = null): string
    {
        $config = array_merge_recursive($this->getParameter('stfalcon_tinymce.config'), $config);
        // Asset package name
        $assetPackageName = (!isset($config['asset_package_name']) ? null : $config['asset_package_name']);
        $browsers = $this->getFileBrowsers($config);

        return $this->getService('templating')->render('StfalconTinymceBundle:Script:init.html.twig', array(
            'tinymce_config'     => $this->getTinyMCEConfiguration($config),
            'asset_package_name' => $assetPackageName,
            'base_url'           => $this->baseUrl,
            'file_browsers'      => $browsers,
            'initializeFunction' => $initializeFunctionName,
        ));
    }

    /**
     * Prepare configuration in JSON
     * @param $config
     * @return string
     */
    private function getTinyMCEConfiguration($config): string
    {
        unset($config['asset_package_name'], $config['init'], $config['theme']);

        if(isset($config['paste_data_images']) && is_string($config['paste_data_images'])) {
            $config['paste_data_images'] = $config['paste_data_images'] === 'true';
        }

        return preg_replace(
            array(
                '/"file_browser_callback":"([^"]+)"\s*/',
                '/"file_picker_callback":"([^"]+)"\s*/',
                '/"paste_preprocess":"([^"]+)"\s*/',
                '/"setup":"([^"]+)"\s*/',
            ),
            array(
                'file_browser_callback:$1',
                'file_picker_callback:$1',
                'paste_preprocess:$1',
                'setup:$1',
            ),
            json_encode($config)
        );
    }

    /**
     * Get all file browsers
     * @param $config
     * @return array
     */
    public function getFileBrowsers(&$config): array
    {
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
            unset($config['file_browser']);
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
            unset($config['file_picker']);
        }

        return $browsers;
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
        /** @var $assets \Symfony\Component\Templating\Helper\CoreAssetsHelper */
        $assets = $this->getService('templating.helper.assets');

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
