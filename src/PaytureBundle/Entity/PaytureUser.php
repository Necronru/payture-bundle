<?php

namespace Necronru\PaytureBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * PaytureUser
 *
 * @ORM\Table(name="payture_user")
 * @ORM\Entity(repositoryClass="Necronru\PaytureBundle\Repository\PaytureUserRepository")
 */
class PaytureUser
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
     * @var string
     *
     * @ORM\Column(name="login", type="string", length=255)
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var integer
     *
     * @ORM\Column(name="phone_number", type="integer", length=255, nullable=true)
     */
    private $phoneNumber;

    /**
     * @var PaytureOrder[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Necronru\PaytureBundle\Entity\PaytureOrder", mappedBy="id")
     */
    private $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
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
     * Set login
     *
     * @param string $login
     *
     * @return PaytureUser
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return PaytureUser
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return integer
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param integer $phoneNumber
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return PaytureOrder[]
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param PaytureOrder[] $orders
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;
    }

}

