<?php

namespace Scott\PassbookBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Record
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Scott\PassbookBundle\Repository\RecordRepository")
 */
class Record
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
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="record")
     * @ORM\JoinColumn(
     *      name="account_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE",
     *      nullable=false
     *      )
     */
    private $account;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_time", type="datetime", nullable=false)
     */
    private $create_time;

    /**
     * @var float
     *
     * @ORM\Column(name="balance", type="decimal", precision=15, scale=2, nullable=false)
     */
    private $balance;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="decimal", precision=15, scale=2, nullable=false)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="memo", type="string", length=50, nullable=true)
     */
    private $memo;

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
     * Set account
     *
     * @param Account $account
     *
     * @return Record
     */
    public function setAccount($account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set create_time
     *
     * @param \DateTime $create_time
     *
     * @return Record
     */
    public function setCreateTime($create_time)
    {
        $this->create_time = $create_time;

        return $this;
    }

    /**
     * Get create_time
     *
     * @return \DateTime
     */
    public function getCreateTime()
    {
        return $this->create_time;
    }

    /**
     * Set balance
     *
     * @param float $balance
     *
     * @return Record
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
     * Set amount
     *
     * @param float $amount
     *
     * @return Record
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
     * Set memo
     *
     * @param string $memo
     *
     * @return Record
     */
    public function setMemo($memo)
    {
        $this->memo = $memo;

        return $this;
    }

    /**
     * Get memo
     *
     * @return string
     */
    public function getMemo()
    {
        return $this->memo;
    }

}

