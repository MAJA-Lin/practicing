<?php

namespace Scott\PassbookBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Account
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Account
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Customer
     *
     * @ORM\OneToOne(targetEntity="Customer")
     * @ORM\JoinColumn(
     *      name="customer_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE",
     *      nullable=false
     *      )
     */
    private $customer;

    /**
     * @ORM\OneToMany(targetEntity="Record", mappedBy="account")
     * @ORM\JoinColumn(name="record_id", referencedColumnName="id")
     */
    private $record;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=10)
     */
    private $currency;

    /**
     * @var float
     *
     * @ORM\Column(
     *      name="balance",
     *      type="decimal",
     *      precision=15,
     *      scale=2
     *      )
     */
    private $balance;

    public function __construct()
    {
        $this->record = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set customer
     *
     * @param Customer $customer
     *
     * @return Account
     */
    public function addCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set currency
     *
     * @param string $currency
     *
     * @return Account
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set balance
     *
     * @param float $balance
     *
     * @return Account
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * Get balance
     *
     * @return float
     */
    public function getBalance()
    {
        return $this->balance;
    }

}