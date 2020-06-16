<?php

namespace Stfalcon\Bundle\TinymceBundle\Model;

use http\Exception\RuntimeException;

/**
 * Interface ConfigManagerInterface
 * @author Łukasz Wątor
 */
interface ConfigManagerInterface
{
    /**
     * @return string
     */
    public function getDefaultConfig();

    /**
     * @param string $defaultConfig
     *
     * @throws RuntimeException
     */
    public function setDefaultConfig($defaultConfig);

    /**
     * @return bool
     */
    public function hasConfigs();

    /**
     * @return array
     */
    public function getConfigs();

    /**
     * @param array $configs
     */
    public function setConfigs(array $configs);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasConfig($name);

    /**
     * @param string $name
     *
     * @return array
     * @throws RuntimeException
     */
    public function getConfig($name);

    /**
     * @param string $name
     * @param array  $config
     */
    public function setConfig($name, array $config);

    /**
     * @param string $name
     * @param array  $config
     */
    public function mergeConfig($name, array $config);
}
