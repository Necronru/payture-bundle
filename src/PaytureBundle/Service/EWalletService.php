<?php


namespace Necronru\PaytureBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use Necronru\Payture\Enum\ErrorCode;
use Necronru\Payture\EWallet\EWallet;
use Necronru\Payture\EWallet\EWalletError;
use Necronru\Payture\EWallet\Payment\Command\InitCommand;
use Necronru\Payture\EWallet\User\Command\CheckCommand;
use Necronru\Payture\EWallet\User\Command\RegisterCommand;
use Necronru\PaytureBundle\Entity\PaytureOrder;
use Necronru\PaytureBundle\Entity\PaytureUser;
use Necronru\PaytureBundle\Event\PaytureNotificationEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EWalletService
{
    /**
     * @var EWallet
     */
    private $eWallet;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(EWallet $eWallet, EntityManagerInterface $entityManager, EventDispatcherInterface $dispatcher)
    {
        $this->eWallet = $eWallet;
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return EWallet
     */
    public function getEWallet(): EWallet
    {
        return $this->eWallet;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    public function createUser($login, $password, $phoneNumber = null): PaytureUser
    {
        $user = $this->getEntityManager()
            ->getRepository('NecronruPaytureBundle:PaytureUser')
            ->findOneBy(['login' => $login]);


        if (!$user) {
            $user = new PaytureUser();
            $user->setLogin($login);
            $user->setPassword($password);
            $user->setPhoneNumber($phoneNumber);
        }

        $this->entityManager->persist($user);

        try {
            $this->eWallet->user()->check(new CheckCommand($user->getLogin(), $user->getPassword()));

        } catch (EWalletError $ex) {

            if (ErrorCode::$codes[ErrorCode::USER_NOT_FOUND] == $ex->getCode()) {

                $this->eWallet->user()->register(new RegisterCommand(
                    $user->getLogin(),
                    $user->getPassword(),
                    $user->getPhoneNumber()
                ));
            } else {
                throw $ex;
            }
        }

        $this->entityManager->flush();


        return $user;
    }

    public function initSession(PaytureOrder $order, $calbackUrl, $ip, $templateTag = null, $cardId = null)
    {
        if (!$order->getSessionId()) {

            $command = new InitCommand(
                $order->getSessionType(),
                $calbackUrl,
                $ip,
                $order->getPaytureUser()->getLogin(),
                $order->getPaytureUser()->getPassword(),
                $order->getUuid(),
                $order->getAmount(),
                $order->getPaytureUser()->getPhoneNumber(),
                $cardId,
                $templateTag,
                'Test'
            );

            $response = $this->eWallet->payment()->init($command);

            $order->setSessionId($response->SessionId);
            $this->entityManager->persist($order);
            $this->entityManager->flush();
        }

        return $this->eWallet->payment()->getSessionLink($order->getSessionId());
    }

    public function handleNotificationData($data)
    {
        $notification = $this->eWallet
            ->notification()
            ->convert($data)
        ;

        $event = new PaytureNotificationEvent($notification, ['data' => $data]);
        return $this->dispatcher->dispatch(get_class($notification), $event);
    }
}