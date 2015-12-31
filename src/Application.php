<?php

namespace LoopbackPHP;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Input\InputOption;

class Application extends \Symfony\Component\Console\Application
{
    const DEFAULT_CONFIG = 'loopback.json';

    /**
     * @var array
     */
    protected $config;

    public function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();

        $defaultCommands[] = new \LoopbackPHP\Commands\Generate\Classes();
        $defaultCommands[] = new \LoopbackPHP\Commands\Generate\Classes\All();
        $defaultCommands[] = new \LoopbackPHP\Commands\Generate\Configs();
        $defaultCommands[] = new \LoopbackPHP\Commands\Generate\Configs\All();

        return $defaultCommands;
    }

    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();

        $inputDefinition->addOptions([
            new InputOption(
                '--config',
                '-c',
                InputOption::VALUE_OPTIONAL,
                'Loopback PHP Generator Config YML File',
                self::DEFAULT_CONFIG
            )
        ]);

        return $inputDefinition;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        if(!isset($this->config)) {
            $this->config = $this->loadConfig();
        }

        return $this->config;
    }

    /**
     * @param string $file
     * @return array
     */
    public function loadConfig($file = self::DEFAULT_CONFIG)
    {
        $configDirectories = ['./'];
        $locator = new FileLocator($configDirectories);
        $file = $locator->locate($file);

        return json_decode(file_get_contents($file), true);
    }
}
