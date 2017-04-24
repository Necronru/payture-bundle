<?php

namespace Necronru\PaytureBundle\Command;

use Necronru\Payture\EWallet\User\Command\DeleteCommand;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PaytureUserDeleteCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('payture:user:delete')
            ->addArgument('login', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ewallet = $this->getContainer()->get('payture.ewallet_service')->getEWallet();

        $login = $input->getArgument('login');

        $response = $ewallet->user()->delete(new DeleteCommand($login));

        dump($response);
    }

}
