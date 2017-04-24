<?php

namespace Necronru\PaytureBundle\Command;

use Necronru\Payture\EWallet\User\Command\UpdateCommand;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PaytureUserUpdateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('payture:user:update')
            ->addArgument('login', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED)
            ->addArgument('phone', InputArgument::OPTIONAL, null)
            ->addArgument('new_login', InputArgument::OPTIONAL, null)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ewallet = $this->getContainer()->get('payture.ewallet_service')->getEWallet();

        $login = $input->getArgument('login');
        $newLogin = $input->getArgument('new_login');
        $password = $input->getArgument('password');
        $phone = $input->getArgument('phone');

        $response = $ewallet->user()->update(new UpdateCommand($login, $password, $phone, $newLogin));

        dump($response);
    }

}
