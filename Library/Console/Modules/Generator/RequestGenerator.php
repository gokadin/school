<?php

namespace Library\Console\Modules\Generator;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RequestGenerator extends Command
{
    const tab = '    ';

    protected function configure()
    {
        $this
            ->setName('make:request')
            ->setDescription('Generates a request.')
            ->addArgument('path');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $root = __DIR__.'/../../../../App/Http/Requests/';
        $fullPath = $root.$input->getArgument('path').'.php';

        $content = '<?php'.PHP_EOL.PHP_EOL;
        $content .= 'namespace App\\Http\\Requests';
        $namespaceSegments = explode('/', $input->getArgument('path'));
        for ($i = 0; $i < sizeof($namespaceSegments) - 1; $i++)
        {
            $content .= '\\'.$namespaceSegments[$i];
        }
        $content .= ';'.PHP_EOL.PHP_EOL;

        if (sizeof($namespaceSegments) > 1)
        {
            $content .= 'use App\\Http\\Requests\\Request;'.PHP_EOL.PHP_EOL;
        }

        $content .= 'class '.$namespaceSegments[sizeof($namespaceSegments) - 1].' extends Request'.PHP_EOL;
        $content .= '{'.PHP_EOL;

        $content .= self::tab.'public function authorize()'.PHP_EOL;
        $content .= self::tab.'{'.PHP_EOL.PHP_EOL;
        $content .= self::tab.'}'.PHP_EOL.PHP_EOL;

        $content .= self::tab.'public function rules()'.PHP_EOL;
        $content .= self::tab.'{'.PHP_EOL.PHP_EOL;
        $content .= self::tab.'}'.PHP_EOL;

        $content .= '}'.PHP_EOL;

        file_put_contents($fullPath, $content);

        $output->writeln('<info>Request generated</info>');
    }
}