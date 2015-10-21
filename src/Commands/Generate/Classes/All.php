<?php

namespace LoopbackPHP\Commands\Generate\Classes;

use LoopbackPHP\Commands\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class All extends Command
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('generate:class:all')
            ->setDescription('Generate all configured models');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->getApplication()->getConfig();
        foreach ($config['models'] as $className => $modelName) {
            $command = $this->getApplication()->find('generate:class');
            $arrayInput = new ArrayInput([
                'className' => $className,
                'modelName' => $modelName
            ], $command->getNativeDefinition());
            $arrayInput->setInteractive(true);
            $command->execute($arrayInput, $output);
        }
    }
}