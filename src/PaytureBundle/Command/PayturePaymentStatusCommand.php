<?php

namespace Necronru\PaytureBundle\Command;

use Necronru\Payture\EWallet\Payment\Command\PayStatusCommand;
use Necronru\Payture\EWallet\User\Command\UpdateCommand;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PayturePaymentStatusCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('payture:payment:status')
            ->addArgument('order_id', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ewallet = $this->getContainer()->get('payture.ewallet_service')->getEWallet();

        $orderId = $input->getArgument('order_id');

        $response = $ewallet->payment()->payStatus(new PayStatusCommand($orderId));

        dump($response);
    }

}
