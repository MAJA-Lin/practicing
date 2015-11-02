<?php

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repositoryClass="ReplyMessageRepository") @Table(name="reply_message")
 */
class ReplyMessage
{
    /**
     * @Id @Column(type="integer") @GeneratedValue
     */
    protected $reply_sn;

    /**
     * Unidirectional - Many-To-One
     *
     * @ManyToOne(targetEntity="Message")
     * @JoinColumn(name="message_sn", referencedColumnName="sn")
     **/
    private $message_table;

    /**
     * @Column(type="text")
     */
    protected $msg;

    /**
     * @Column(type="datetime")
     */
    protected $time;

    /**
     * @Column(type="string", length=20, nullable=TRUE)
     */
    protected $name;

    public function getMessageTable()
    {
        return $this->message_table;
    }
    public function setMessageTable(Message $m)
    {
        $this->message_table = $m;
    }

    public function getReplySn()
    {
        return $this->reply_sn;
    }

    public function getMsg()
    {
        return $this->msg;
    }

    public function setMsg($msg)
    {
        $this->msg = $msg;
    }

    public function setTime()
    {
        $this->time = date_create(date("Y-m-d H:i:s"));
    }

    public function getTime()
    {
        return $this->time;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

}