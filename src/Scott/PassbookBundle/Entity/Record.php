<?php

namespace Scott\PassbookBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Record
 *
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
     * @param Account $account
     * @param \DateTime $create_time
     * @param float $balance
     * @param float $amount
     */
    public function __construct(Account $account, \DateTime $create_time, $balance, $amount)
    {
        $this->account = $account;
        $this->create_time = $create_time;
        $this->balance = $balance;
        $this->amount = $amount;
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
     * Get account
     *
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
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
     * Get balance
     *
     * @return float
     */
    public function getBalance()
    {
        return $this->balance;
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

    /**
     * Return array
     *
     * @return array
     */
    public function toArray()
    {
        $array = [
            'id' => $this->id,
            'accountId' => $this->account->getId(),
            'create_time' => $this->create_time,
            'balance' => $this->balance,
            'amount' => $this->amount,
            'memo' => $this->memo,
        ];

        return $array;
    }
}

