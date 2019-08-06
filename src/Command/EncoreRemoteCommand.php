<?php

namespace Postyou\WebpackEncoreRemoteBundle\Command;

use Postyou\WebpackEncoreRemoteBundle\Conversion\Converter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EncoreRemoteCommand extends Command
{

    protected function configure(): void
    {
        $this
            ->setName('encore:remote')
            ->setDescription('Trigger the encore remote compilation.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $converter = new Converter();
        $converter->doConversion(false);

        return 0;
    }

}
