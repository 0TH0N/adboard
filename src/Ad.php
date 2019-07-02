<?php

namespace AdBoard;

class Ad
{
    private $id;
    private $adText;
    private $userName;
    private $password;
    private $phone;
    private $postDate;

    public function __construct($id, $adText, $userName, $password, $phone, $postDate)
    {
        $this->id = $id;
        $this->adText = $adText;
        $this->userName = $userName;
        $this->password = $password;
        $this->phone = $phone;
        $this->postDate = $postDate;
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of ad_text
     */
    public function getAdText()
    {
        return $this->adText;
    }

    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the value of password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Get the value of phone
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Get the value of post_date
     */
    public function getPostDate()
    {
        return $this->postDate;
    }
}
