<?php
namespace App\Db;

use Bs\Db\Traits\TimestampTrait;
use Bs\Db\Traits\UserTrait;

/**
 * @author Mick Mifsud
 * @created 2021-10-22
 * @link http://tropotek.com.au/
 * @license Copyright 2021 Tropotek
 */
class Auth extends \Tk\Db\Map\Model implements \Tk\ValidInterface
{
    use UserTrait;
    use TimestampTrait;

    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var int
     */
    public $userId = 0;

    /**
     * @var string
     */
    public $name = '';

    /**
     * @var string
     */
    public $url = '';

    /**
     * @var string
     */
    public $username = '';

    /**
     * @var string
     */
    public $password = '';

    /**
     * @var string
     */
    public $authtool = '';

    /**
     * @var string
     */
    public $keys = '';

    /**
     * @var string
     */
    public $notes = '';

    /**
     * @var \DateTime
     */
    public $modified = null;

    /**
     * @var \DateTime
     */
    public $created = null;


    /**
     * Auth
     */
    public function __construct()
    {
        $this->_TimestampTrait();
        if ($this->getConfig()->getAuthUser())
            $this->setUserId($this->getConfig()->getAuthUser());
    }

    /**
     * @param string $name
     * @return Auth
     */
    public function setName($name) : Auth
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param string $url
     * @return Auth
     */
    public function setUrl($url) : Auth
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl() : string
    {
        return $this->url;
    }

    /**
     * @param string $username
     * @return Auth
     */
    public function setUsername($username) : Auth
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername() : string
    {
        return $this->username;
    }

    /**
     * @param string $password
     * @return Auth
     */
    public function setPassword($password) : Auth
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword() : string
    {
        return $this->password;
    }

    /**
     * @param string $authtool
     * @return Auth
     */
    public function setAuthtool($authtool) : Auth
    {
        $this->authtool = $authtool;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthtool() : string
    {
        return $this->authtool;
    }

    /**
     * @param string $keys
     * @return Auth
     */
    public function setKeys($keys) : Auth
    {
        $this->keys = $keys;
        return $this;
    }

    /**
     * @return string
     */
    public function getKeys() : string
    {
        return $this->keys;
    }

    /**
     * @param string $notes
     * @return Auth
     */
    public function setNotes($notes) : Auth
    {
        $this->notes = $notes;
        return $this;
    }

    /**
     * @return string
     */
    public function getNotes() : string
    {
        return $this->notes;
    }

    /**
     * @return array
     */
    public function validate()
    {
        $errors = array();

        if (!$this->userId) {
            $errors['userId'] = 'Invalid value: userId';
        }

        if (!$this->name) {
            $errors['name'] = 'Invalid value: name';
        }

//        if (!$this->url) {
//            $errors['url'] = 'Invalid value: url';
//        }

//        if (!$this->username) {
//            $errors['username'] = 'Invalid value: username';
//        }

//        if (!$this->password) {
//            $errors['password'] = 'Invalid value: password';
//        }

//        if (!$this->authtool) {
//            $errors['authtool'] = 'Invalid value: authtool';
//        }

        return $errors;
    }

}
