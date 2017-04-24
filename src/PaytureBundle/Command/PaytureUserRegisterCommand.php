<?php

namespace Necronru\PaytureBundle\Command;

use Necronru\Payture\EWallet\User\Command\RegisterCommand;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PaytureUserRegisterCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('payture:user:register')
            ->addArgument('login', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED)
            ->addArgument('phone', InputArgument::OPTIONAL, null)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ewallet = $this->getContainer()->get('payture.ewallet_service')->getEWallet();

        $login = $input->getArgument('login');
        $password = $input->getArgument('password');
        $phone = $input->getArgument('phone');

        $response = $ewallet->user()->register(new RegisterCommand($login, $password, $phone));

        dump($response);
    }

}
