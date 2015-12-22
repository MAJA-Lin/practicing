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
     * @ORM\Version
     * @ORM\Column(type="integer")
     */
     private $version;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=10)
     */
    private $currency;

    /**
     * @var float
     *
     * @ORM\Column(name="balance", type="decimal", precision=15, scale=2)
     */
    private $balance;

    /**
     * 設置customer與currency, 並設置balance的初始值為0
     *
     * @param Customer $customer
     * @param string $currency
     */
    public function __construct(Customer $customer, $currency)
    {
        $this->customer = $customer;
        $customer->setAccount($this);

        $this->currency = $currency;
        $this->record = new ArrayCollection();
        $this->balance = 0;
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
     * Get version
     *
     * @return integer
     */
     public function getVersion()
     {
         return $this->version;
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

    /**
     * Return array
     *
     * @return array
     */
    public function toArray()
    {
        $array = [
            'id' => $this->id,
            'customerId' => $this->customer->getId(),
            'currency' => $this->currency,
            'balance' => $this->balance,
        ];

        return $array;
    }
}
