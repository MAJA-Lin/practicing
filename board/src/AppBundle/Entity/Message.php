<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="MessageRepository") @ORM\Table(name="message")
 */
class Message
{
    /**
     * @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="text")
     */
    protected $msg;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $time;

    /**
     * @ORM\Column(type="string", length=20, nullable=TRUE)
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="ReplyMessage", mappedBy="message")
     * @ORM\JoinColumn(name="reply_id", referencedColumnName="id")
     */
    protected $reply;

    public function __construct()
    {
        $this->reply = new ArrayCollection();
    }

    public function getReply()
    {
        return $this->reply;
    }

    public function setReply($reply)
    {
        $this->reply = $reply;
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