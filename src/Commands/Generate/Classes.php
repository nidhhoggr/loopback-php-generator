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
            )
            ->addArgument(
                'endPoint',
                InputArgument::OPTIONAL,
                'Loopback model endpoint'
            );

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->getApplication()->getConfig();
        $dialog = $this->getHelper('dialog');
        $className = $input->getArgument('className');
        $modelName = $input->getArgument('modelName');
        $endPoint = $input->getArgument('endPoint');

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

        $modelConfig = ['properties' => $model->properties];
        $configsDirectory = $config['build']['configs'];
        $configPath = realpath($configsDirectory . '/' . $modelName . '.json');
        if (file_exists($configPath)) {
            $modelConfig = json_decode(file_get_contents($configPath), true);
        }

        $namespace = new PhpNamespace($config['namespace']);
        $namespace->addUse($config['extends']);

        $class = new ClassType($className, $namespace);
        $class->addExtend($config['extends']);

        if(!empty($endPoint)) {
          $class->addConst("ENDPOINT", $endPoint);
        }

        foreach ($model->properties as $propertyName => $propertyDef) {
            if(in_array($propertyName, $modelConfig['properties'], true)) {
              
                $property = $class->addProperty($propertyName)->setVisibility('public');
           
                $accessorMethod = $class->addMethod($this->toCamelCase("get_" . ($propertyName)));
                
                $accessorMethod->setBody('return $this->' . $propertyName . ';');
         
                $mutatorMethod = $class->addMethod($this->toCamelCase("set_" . ($propertyName)));
       
                $mutatorMethod->addParameter($propertyName);
     
                $mutatorMethod->setBody('$this->' . $propertyName . ' = $'.$propertyName.';');
   
                if (is_string($propertyDef['type'])) {
                    $property->addDocument("@var {$propertyDef['type']}");
                } else {
                    $property->addDocument("@var mixed");
                }
            } else {
                $output->writeln(sprintf("<info>Skipped property %s</info>", $propertyName));
            }
        }

        file_put_contents($buildPath, str_replace("\t", "    ", "<?php\n{$namespace}{$class}"));

        // TODO: replace with PHP_CodeSniffer library
        exec(sprintf('vendor/bin/phpcbf --standard=PSR2 --encoding=utf-8 "%s"', $buildPath));

        $output->writeln(sprintf("<info>Class %s created</info>", $buildPath));
    }

    private function toCamelCase($str, $capitalise_first_char = false) {
        if($capitalise_first_char) {
            $str[0] = strtoupper($str[0]);
        }
        $func = create_function('$c', 'return strtoupper($c[1]);');
        return preg_replace_callback('/_([a-z])/', $func, $str);
    }
}
