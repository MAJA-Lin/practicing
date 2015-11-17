<?php

namespace Scott\BoardBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity @ORM\Table(name="reply_message")
 */
class ReplyMessage
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * Unidirectional - Many-To-One
     *
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="reply")
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    private $message;

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

    public function __construct()
    {
        $this->message = new ArrayCollection();
    }

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
        $this->time = new \DateTime();
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
