<?php

namespace Necronru\PaytureBundle\Command;

use Necronru\Payture\EWallet\Card\Command\GetCardListCommand;
use Necronru\Payture\EWallet\User\Command\CheckCommand;
use Necronru\Payture\EWallet\User\Command\RegisterCommand;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PaytureUserCheckCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('payture:user:check')
            ->addArgument('login', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED)
            ->addArgument('phone', InputArgument::OPTIONAL)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ewallet = $this->getContainer()->get('payture.ewallet_service')->getEWallet();

        $login = $input->getArgument('login');
        $password = $input->getArgument('password');

        $command = new CheckCommand($login, $password);

        $response = $ewallet->user()->check($command);

        dump($response);
    }

}
