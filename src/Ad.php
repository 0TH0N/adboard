<?php

namespace AdBoard;

class Ad
{
    protected $id;
    protected $adText;
    protected $userName;
    protected $password;
    protected $phone;
    protected $postDate;

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
     * Set the value of adText
     *
     * @return self
     */
    public function setAdText($adText)
    {
        $this->adText = $adText;

        return $this;
    }

    /**
     * Get the value of name
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Set the value of userName
     *
     * @return self
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;

        return $this;
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
     * Set the value of phone
     *
     * @return self
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get the value of post_date
     */
    public function getPostDate()
    {
        return $this->postDate;
    }

    /**
     * Set the value of postDate
     *
     * @return self
     */
    public function setPostDate($postDate)
    {
        $this->postDate = $postDate;

        return $this;
    }
}
