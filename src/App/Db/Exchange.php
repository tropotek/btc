<?php
namespace App\Db;

use ccxt\ExchangeError;
use Tk\Db\Data;
use Tk\Db\Exception;

/**
 * @author Mick Mifsud
 * @created 2019-05-30
 * @link http://tropotek.com.au/
 * @license Copyright 2019 Tropotek
 */
class Exchange extends \Tk\Db\Map\Model implements \Tk\ValidInterface
{
    const MARKET_ALL = 'ALL';       // All purchased coin values
    const MARKET_TOTAL = 'TOTAL';   // Value of all coins and Dollars in exchange over time

    use \Bs\Db\Traits\UserTrait;
    use \Bs\Db\Traits\TimestampTrait;

    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var int
     */
    public $userId = 0;

//    /**
//     * @var bool
//     */
//    public $default = false;

    /**
     * @var string
     */
    public $driver = '';

    /**
     * @var string
     */
    public $username = '';

    /**
     * @var string
     */
    public $apiKey = '';

    /**
     * @var string
     */
    public $secret = '';

    /**
     * @var string
     */
    public $currency = 'AUD';

    /**
     * @var string
     */
    public $icon = 'fa fa-btc';

    /**
     * @var string
     */
    public $description = '';

    /**
     * @var string
     */
    public $active = true;

    /**
     * @var string
     */
    public $options = '';

    /**
     * @var \DateTime
     */
    public $modified = null;

    /**
     * @var \DateTime
     */
    public $created = null;

    /**
     * @var \ccxt\Exchange
     */
    protected $_api;

    /**
     * @var Data
     */
    protected $_data = null;


    /**
     * Exchange
     */
    public function __construct()
    {
        $this->_TimestampTrait();
    }

    /**
     * Create a valid list of exchange API's
     *
     * @return array
     */
    public static function getDriverList()
    {
        $list = \Tk\Form\Field\Select::arrayToSelectList(\ccxt\Exchange::$exchanges);
//        array_shift($list);
//        array_shift($list);
        //$list['coinspot'] = '\\App\\Driver\\coinspot';
        //$list['Btcmarkets V3'] = '\\App\\Driver\\BtcMarkets3';
        ksort($list);
        return $list;
    }

    /**
     * Get the institution data object
     *
     * @return Data
     */
    public function getData()
    {
        if (!$this->_data)
            $this->_data = Data::create(get_class($this), $this->getVolatileId());
        return $this->_data;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return ucfirst($this->driver);
    }

    /**
     * @param $marketId
     * @return bool
     */
    public function hasMarket($marketId)
    {
        $this->getApi()->load_markets();
        $markets = array_keys($this->getApi()->fetch_markets());
        return in_array($marketId, $markets);
    }

    /**
     * Live Call
     * Return the CCXT Exchange API object
     * @return \ccxt\Exchange
     */
    public function getApi()
    {
        if (!$this->_api) {
            $driver = $this->driver;
            if ($driver == 'coinspot') {
                $driver = '\\App\\Driver\\coinspot';
            }
            if (!class_exists($driver)) {
                $driver = '\\ccxt\\' . $this->driver;
            }

            if (class_exists($driver)) {
                $this->_api = new $driver(array(
                    'apiKey' => $this->apiKey,
                    'secret' => $this->secret
                    // TODO: add more options over time as needed
                ));
            }
        }
        return $this->_api;
    }

    /**
     * @return string
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @param string $driver
     * @return Exchange
     */
    public function setDriver(string $driver)
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return Exchange
     */
    public function setUsername(string $username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     * @return Exchange
     */
    public function setApiKey(string $apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     * @return Exchange
     */
    public function setSecret(string $secret)
    {
        $this->secret = $secret;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return strtoupper($this->currency);
    }

    /**
     * @param string $currency
     * @return Exchange
     */
    public function setCurrency(string $currency)
    {
        $this->currency = strtoupper($currency);
        return $this;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     * @return Exchange
     */
    public function setIcon(string $icon)
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Exchange
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param string $active
     * @return Exchange
     */
    public function setActive(string $active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @param $number
     * @param int $precision
     * @return string
     */
    public static function truncateToString($number, $precision = 0)
    {
        return \ccxt\Exchange::truncate_to_string($number, $precision);
    }

    /**
     * @param $number
     * @param int $precision
     * @return string
     */
    public static function toFloat($number, $precision = 4)
    {
        return sprintf('%.'.$precision.'f', self::truncateToString($number));
    }

    /**
     * return the status list for a select field
     * @param null|string $currentId
     * @return array
     */
    public static function getSelectList($currentId = null)
    {
        $arr = ExchangeMap::create()->findFiltered(['active' => true]);
        $arr2 = array();
        foreach ($arr as $k => $obj) {
            $k = $obj->getName() . ' [' . $obj->getCurrency() . ']';
            if ($obj->getId() == $currentId) {
                $k .= ' (Current)';
            }
            $arr2[$k] = $obj->getId();
            $arr = $arr2;
        }
        return $arr;
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

        if (!$this->driver) {
            $errors['driver'] = 'Invalid value: driver';
        }

        if (!$this->currency) {
            $errors['currency'] = 'Invalid value: currency';
        }

        if (!$this->icon) {
            $errors['icon'] = 'Invalid value: icon';
        }

        if (!$this->apiKey) {
            $errors['apiKey'] = 'Invalid value: apiKey';
        }

        if (!$this->secret) {
            $errors['secret'] = 'Invalid value: secret';
        }

        
        return $errors;
    }

}

