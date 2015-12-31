<?php

namespace LoopbackPHP\Commands\Generate\Configs;

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
            ->setName('generate:config:all')
            ->setDescription('Generate all configured models configs');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->getApplication()->getConfig();



        foreach ($config['models'] as $className => $model) {

            if(is_array($model)) {
              $arrayInputArgs = [
                'modelName' => $model["modelName"]
              ];
            }
            else {
               $arrayInputArgs = [
                'modelName' => $model
              ];
            }

            $command = $this->getApplication()->find('generate:config');
            $arrayInput = new ArrayInput($arrayInputArgs, $command->getNativeDefinition());
            $arrayInput->setInteractive(true);
            $command->execute($arrayInput, $output);
        }
    }
}
