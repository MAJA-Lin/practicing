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
    protected $id;

    /**
     * Unidirectional - Many-To-One
     *
     * @ManyToOne(targetEntity="Message")
     **/
    private $message;

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

    public function getMessage()
    {
        return $this->message;
    }
    public function setMessage(Message $m)
    {
        $this->message = $m;
    }

    public function getId()
    {
        return $this->id;
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