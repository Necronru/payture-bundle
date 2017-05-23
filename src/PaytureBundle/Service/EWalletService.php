<?php


namespace Necronru\PaytureBundle\Service;


use Ahml\BillingBundle\Builder\CardAddSessionBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Necronru\Payture\Enum\ErrorCode;
use Necronru\Payture\Enum\TransactionStatus;
use Necronru\Payture\EWallet\Card\Command\GetCardListCommand;
use Necronru\Payture\EWallet\Card\Response\GetCardList\Item as PaytureCard;
use Necronru\Payture\EWallet\EWallet;
use Necronru\Payture\EWallet\EWalletError;
use Necronru\Payture\EWallet\Payment\Command\InitCommand;
use Necronru\Payture\EWallet\Payment\Enum\SessionType;
use Necronru\Payture\EWallet\User\Command\CheckCommand;
use Necronru\Payture\EWallet\User\Command\RegisterCommand;
use Necronru\PaytureBundle\Entity\AbstractPaytureOrder;
use Necronru\PaytureBundle\Entity\PaytureUser;
use Necronru\PaytureBundle\Event\PaytureNotificationEvent;
use Psr\Log\LoggerInterface;
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

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(EWallet $eWallet, EntityManagerInterface $entityManager, EventDispatcherInterface $dispatcher)
    {
        $this->eWallet = $eWallet;
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return EWallet
     */
    public function getEWallet()
    {
        return $this->eWallet;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * Получить список карт пользователя
     *
     * @param PaytureUser $user
     * @return mixed|\Necronru\Payture\EWallet\Card\Response\GetCardList\GetList
     */
    public function fetchCardList(PaytureUser $user)
    {
        $response =  $this->getEWallet()->card()->getList(new GetCardListCommand($user->getLogin(), $user->getPassword()));

        return $cards = array_map(function ($card) {
            /** @var PaytureCard $card */

            return [
                'card_id' => $card->CardId,
                'card_name' => $card->CardName,
                'card_holder' => $card->CardHolder,
                'status' => $card->Status,
                'no_cvv' => 'true' == $card->NoCVV ? true : false,
                'expired' => 'true' == $card->Expired ? true : false
            ];
        }, (array)$response->Item);
    }

    /**
     * Создать и зарегестрировать пользователя
     *
     * @param $login
     * @param $password
     * @param null $phoneNumber
     * @return PaytureUser|null|object
     * @throws EWalletError
     */
    public function createUser($login, $password, $phoneNumber = null)
    {
        $uniqId = uniqid('payture_create_user.');

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

            $command = new CheckCommand($user->getLogin(), $user->getPassword());

            $this->log('try find payture user', [
                'uniqid' => $uniqId,
                'command' => json_encode($command),
                'commandClass' => get_class($command)
            ]);

            $this->eWallet->user()->check($command);

        } catch (EWalletError $ex) {

            if (ErrorCode::$codes[ErrorCode::USER_NOT_FOUND] == $ex->getCode()) {

                $command = new RegisterCommand(
                    $user->getLogin(),
                    $user->getPassword(),
                    $user->getPhoneNumber()
                );

                $this->log('try to register payture user', [
                    'uniqid' => $uniqId,
                    'command' => json_encode($command),
                    'commandClass' => get_class($command)
                ]);

                $this->eWallet->user()->register($command);

            } else {
                $this->log('failed register user', ['uniqid' => $uniqId, 'mesasge' => $ex->getMessage()], 'critical');
                throw $ex;
            }
        }

        $this->entityManager->flush();


        return $user;
    }

    public function initSession(AbstractPaytureOrder $order, $calbackUrl, $ip, $templateTag = null, $cardId = null, $productName = null)
    {
        $uniqId = uniqid('payture_init_session.');

        if (!$order->getSessionId()) {

            $command = new InitCommand(
                $order->getSessionType(),
                $calbackUrl,
                $ip,
                $order->getPaytureUser()->getLogin(),
                $order->getPaytureUser()->getPassword(),
                $order->getUuid(),
                $order->getAmount() * 100, // Сумма платежа в копейках
                $order->getPaytureUser()->getPhoneNumber(),
                $cardId,
                $templateTag,
                null,
                $order->getAmount(),
                $productName
            );

            $this->log('try init session', [
                'orderId' => $order->getId(),
                'uniqid' => $uniqId,
                'command' => json_encode($command),
                'commandClass' => get_class($command)
            ], 'debug');

            try {
                $response = $this->eWallet->payment()->init($command);
            } catch (EWalletError $ex) {
                $this->log('failed init session', ['uniqid' => $uniqId, 'message' => $ex->getMessage()], 'critical');
                throw $ex;
            }

            $order->setSessionId($response->SessionId);
            $order->setStatus(TransactionStatus::SESSION_INITED);

            $this->entityManager->persist($order);
            $this->entityManager->flush();
        }

        return $this->eWallet->payment()->getSessionLink($order->getSessionId());
    }

    public function handleNotificationData($data)
    {
        $notification = $this->eWallet
            ->notification()
            ->convert($data);

        $event = new PaytureNotificationEvent($notification, ['data' => $data]);
        return $this->dispatcher->dispatch(get_class($notification), $event);
    }

    protected function log($message, $context = [], $level = 'debug') {

        if (!$this->logger) {
            return;
        }

        $this->logger->log($level, $message, $context);
    }

    public function initAddSession(PaytureUser $user, $callbackUrl, $ip, $templateTag = null)
    {
        $command = new InitCommand(
            SessionType::ADD,
            $callbackUrl,
            $ip,
            $user->getLogin(),
            $user->getPassword(),
            null,
            null,
            null,
            null,
            $templateTag
        );

        $response = $this->getEWallet()->payment()->init($command);

        return $this->getEWallet()->payment()->getSessionLink($response->SessionId);
    }
}