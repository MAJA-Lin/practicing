<?php
/**
 * @Entity(repositoryClass="MessageRepository") @Table(name="message")
 */
class Message
{
    /**
     * @Id @Column(type="integer") @GeneratedValue
     */
    protected $id;

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