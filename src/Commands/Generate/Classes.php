<?php
namespace LoopbackPHP\Commands\Generate;

use LoopbackPHP\Commands\Command;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
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
     * TODO: integrate with build/configs
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->getApplication()->getConfig();
        $dialog = $this->getHelper('dialog');
        $className = $input->getArgument('className');
        $modelName = $input->getArgument('modelName');

        $model = $this->getModel($modelName);

        $buildDirectory = $config['build']['classes'];
        $buildPath = $buildDirectory . '/' . $className . '.php';
        if (file_exists($buildPath)) {
            if (!$dialog->askConfirmation(
                $output,
                sprintf('<question>Class file "%s" exists, overwrite?</question>', $buildPath),
                false
            )
            ) {
                return;
            }
        }

        $modelConfig = ['properties' => []];
        foreach ($model->properties as $property => $propertyDef) {
            $modelConfig['properties'][] = $property;
        }

        $namespace = new PhpNamespace('Crowdsdom\\Models');

        $class = new ClassType($className, $namespace);
        $class->addExtend('Crowdsdom\\Models\\Model');

        foreach ($model->properties as $property => $propertyDef) {
            $property = $class->addProperty($property)->setVisibility('public');

            if(is_string($propertyDef['type'])) {
                $property->addDocument("@var {$propertyDef['type']}");
            } else {
                $property->addDocument("@var mixed");
            }
        }

        file_put_contents($buildPath, str_replace("\t", "    ", "<?php\n{$namespace}{$class}"));

        $output->writeln(sprintf("<info>Class %s created</info>", $buildPath));
    }
}
