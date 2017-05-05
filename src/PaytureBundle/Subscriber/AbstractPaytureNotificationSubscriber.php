<?php


namespace Necronru\PaytureBundle\Subscriber;


use Necronru\PaytureBundle\Event\PaytureNotificationEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class AbstractPaytureNotificationSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            PaytureNotificationEvent::CUSTOMER_ADD_FAIL => 'onCustomerAddFail',
            PaytureNotificationEvent::CUSTOMER_ADD_SUCCESS => 'onCustomerAddSuccess',
            PaytureNotificationEvent::CUSTOMER_PAY_FAIL => 'onCustomerPayFail',
            PaytureNotificationEvent::CUSTOMER_PAY_SUCCESS => 'onCustomerPaySuccess',
            PaytureNotificationEvent::CUSTOMER_REFUND_SUCCESS => 'onCustomerRefundSuccess',
            PaytureNotificationEvent::CUSTOMER_REFUND_FAIL => 'onCustomerRefundFail',
            PaytureNotificationEvent::CUSTOMER_SEND_CODE => 'onCustomerSendCode',
        ];
    }

    abstract public function onCustomerAddFail(PaytureNotificationEvent $event, $eventName, EventDispatcherInterface $dispatcher);

    abstract public function onCustomerAddSuccess(PaytureNotificationEvent $event, $eventName, EventDispatcherInterface $dispatcher);

    abstract public function onCustomerPayFail(PaytureNotificationEvent $event, $eventName, EventDispatcherInterface $dispatcher);

    abstract public function onCustomerPaySuccess(PaytureNotificationEvent $event, $eventName, EventDispatcherInterface $dispatcher);

    abstract public function onCustomerRefundSuccess(PaytureNotificationEvent $event, $eventName, EventDispatcherInterface $dispatcher);

    abstract public function onCustomerRefundFail(PaytureNotificationEvent $event, $eventName, EventDispatcherInterface $dispatcher);

    abstract public function onCustomerSendCode(PaytureNotificationEvent $event, $eventName, EventDispatcherInterface $dispatcher);
}