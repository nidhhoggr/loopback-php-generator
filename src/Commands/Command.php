<?php

namespace LoopbackPHP\Commands;

use LoopbackPHP\Loopback\Model;

abstract class Command extends \Symfony\Component\Console\Command\Command
{
    /**
     * @param string $modelName
     * @return Model
     */
    protected function getModel($modelName)
    {
        $config = $this->getApplication()->getConfig();

        $modelsDirectory = $config['loopback']['modelsDirectory'];
        $modelPath = realpath($modelsDirectory . '/' . $modelName . '.json');
        return new Model($modelPath);
    }
}
