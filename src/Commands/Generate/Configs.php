<?php
namespace LoopbackPHP\Commands\Generate;

use LoopbackPHP\Loopback\Model;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Configs extends Command
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('generate:config')
            ->setDescription('Model Configs generator')
            ->addArgument(
                'modelName',
                InputArgument::REQUIRED,
                'Loopback model name'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelper('dialog');
        $modelName = $input->getArgument('modelName');

        $config = $this->getApplication()->getConfig();

        $modelsDirectory = $config['loopback']['modelsDirectory'];
        $modelPath = realpath($modelsDirectory . '/' . $modelName . '.json');
        $model = new Model($modelPath);

        $buildDirectory = $config['build']['configs'];
        $buildPath = $buildDirectory . '/' . $modelName . '.json';
        if(file_exists($buildPath)) {
            if (!$dialog->askConfirmation(
                $output,
                sprintf('<question>Model config file "%s" exists, overwrite?</question>', $buildPath),
                false
            )) {
                return;
            }
        }

        $modelConfig = ['properties' => []];
        foreach ($model->properties as $property => $propertyDef) {
            $modelConfig['properties'][] = $property;
        }

        file_put_contents($buildPath, json_encode($modelConfig, \JSON_PRETTY_PRINT));

        $output->writeln(sprintf("<info>Model config %s created</info>", $buildPath));
    }
}
