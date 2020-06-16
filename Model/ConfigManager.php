<?php

namespace Stfalcon\Bundle\TinymceBundle\Model;

use http\Exception\RuntimeException;

/**
 * Class ConfigManager
 * @author Łukasz Wątor
 */
class ConfigManager implements ConfigManagerInterface
{
    /**
     * @var string
     */
    private $defaultConfig;

    /**
     * @var array
     */
    private $configs = [];

    /**
     * @param array       $configs
     * @param string|null $defaultConfig
     */
    public function __construct(array $configs = [], $defaultConfig = null)
    {
        $this->setConfigs($configs);

        if ($defaultConfig !== null) {
            $this->setDefaultConfig($defaultConfig);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultConfig()
    {
        return $this->defaultConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultConfig($defaultConfig)
    {
        if (!$this->hasConfig($defaultConfig)) {
            throw new RuntimeException($defaultConfig);
        }

        $this->defaultConfig = $defaultConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function hasConfigs()
    {
        return !empty($this->configs);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigs()
    {
        return $this->configs;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfigs(array $configs)
    {
        foreach ($configs as $name => $config) {
            $this->setConfig($name, $config);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasConfig($name)
    {
        return isset($this->configs[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig($name)
    {
        if (!$this->hasConfig($name)) {
            throw new RuntimeException($name);
        }

        return $this->configs[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig($name, array $config)
    {
        $this->configs[$name] = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeConfig($name, array $config)
    {
        $this->configs[$name] = array_merge($this->getConfig($name), $config);
    }
}
