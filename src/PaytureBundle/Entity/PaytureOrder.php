<?php

namespace Necronru\PaytureBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Necronru\Payture\EWallet\Payment\Enum\SessionType;
use Ramsey\Uuid\Uuid;

/**
 * PaytureOrder
 *
 * @ORM\Table(name="payture_order")
 * @ORM\Entity(repositoryClass="Necronru\PaytureBundle\Repository\PaytureOrderRepository")
 */
class PaytureOrder
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=255, unique=true)
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var PaytureUser
     *
     * @ORM\ManyToOne(targetEntity="Necronru\PaytureBundle\Entity\PaytureUser", cascade={"persist", "remove"})
     */
    private $paytureUser;

    /**
     * @var string
     *
     * @ORM\Column(name="session_id", type="string", length=255, nullable=true)
     */
    private $sessionId;

    /**
     * @var string SessionType::PAY|SessionType::BLOCK
     *
     * @ORM\Column(name="session_type", type="string", length=255)
     */
    private $sessionType;

    public function __construct()
    {
        $this->uuid = (string) Uuid::uuid5(Uuid::NAMESPACE_DNS, uniqid());
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set amount
     *
     * @param float $amount
     *
     * @return PaytureOrder
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return PaytureOrder
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return PaytureOrder
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return PaytureUser
     */
    public function getPaytureUser()
    {
        return $this->paytureUser;
    }

    /**
     * @param PaytureUser $paytureUser
     */
    public function setPaytureUser(PaytureUser $paytureUser)
    {
        $this->paytureUser = $paytureUser;
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @param string $sessionId
     */
    public function setSessionId(string $sessionId)
    {
        $this->sessionId = $sessionId;
    }

    /**
     * @return string
     */
    public function getSessionType()
    {
        return $this->sessionType;
    }

    /**
     * @param string $sessionType
     */
    public function setSessionType($sessionType)
    {
        $this->sessionType = $sessionType;
    }
}

