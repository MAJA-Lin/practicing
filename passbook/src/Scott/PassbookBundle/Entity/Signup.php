<?php

namespace Scott\PassbookBundle\Entity;

/**
 *
 */
class Signup
{
    protected $email;
    protected $password;
    protected $password_confirmed;
    protected $currency;

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPasswordConfirmed()
    {
        return $this->password_confirmed;
    }

    public function setPasswordConfirmed($password_confirmed)
    {
        $this->password_confirmed = $password_confirmed;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

}


