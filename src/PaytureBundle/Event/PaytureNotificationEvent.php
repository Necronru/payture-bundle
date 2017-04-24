<?php


namespace Necronru\PaytureBundle\Event;


use Necronru\Payture\EWallet\AbstractNotification;
use Symfony\Component\EventDispatcher\GenericEvent;
use Necronru\Payture\EWallet\Notification\Command as Notification;

class PaytureNotificationEvent extends GenericEvent
{
    const CUSTOMER_ADD_FAIL       = Notification\CustomerAddFail::class;
    const CUSTOMER_ADD_SUCCESS    = Notification\CustomerAddSuccess::class;
    const CUSTOMER_PAY_FAIL       = Notification\CustomerPayFail::class;
    const CUSTOMER_PAY_SUCCESS    = Notification\CustomerPaySuccess::class;
    const CUSTOMER_REFUND_SUCCESS = Notification\CustomerRefundSuccess::class;
    const CUSTOMER_REFUND_FAIL    = Notification\CustomerRefundFail::class;
    const CUSTOMER_SEND_CODE      = Notification\CustomerSendCode::class;

    /**
     * @return AbstractNotification
     */
    public function getSubject()
    {
        return parent::getSubject();
    }


}