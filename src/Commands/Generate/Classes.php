<?php
namespace LoopbackPHP\Commands\Generate;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Classes extends Command
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('generate:class')
            ->setDescription('Classes generator')
            ->addArgument(
                'className',
                InputArgument::REQUIRED,
                'PHP class name'
            )
            ->addArgument(
                'modelName',
                InputArgument::REQUIRED,
                'Loopback model name'
            );
    }

    /**
     * TODO: parse model-config.json instead of directly mapping modelName
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->getApplication()->getConfig();

        $className = $input->getArgument('className');
        $modelName = $input->getArgument('modelName');

        print_r($config);
    }
}
