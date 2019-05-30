<?php
namespace App\Db;

/**
 * @author Mick Mifsud
 * @created 2019-05-30
 * @link http://tropotek.com.au/
 * @license Copyright 2019 Tropotek
 */
class Exchange extends \Tk\Db\Map\Model implements \Tk\ValidInterface
{

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
    protected $_exchange;


    /**
     * Exchange
     */
    public function __construct()
    {
        $this->modified = new \DateTime();
        $this->created = new \DateTime();

    }

    /**
     * @return string
     */
    public function getName()
    {
        return ucfirst($this->driver);
    }

    /**
     * Return the CCXT Exchange API object
     * @return \ccxt\Exchange
     */
    public function getExchangeObj()
    {
        if (!$this->_exchange) {
            $driver = '\\ccxt\\' . $this->driver;
            $this->_exchange = new $driver(array(
                'apiKey' => $this->apiKey,
                'secret' => $this->secret
                // TODO: add more options over time as needed
            ));
        }
        return $this->_exchange;
    }
    /**
     * Here's an overview of base exchange properties with values added for example:

     {
    'id':   'exchange'                  // lowercase string exchange id
    'name': 'Exchange'                  // human-readable string
    'countries': [ 'US', 'CN', 'EU' ],  // array of ISO country codes
    'urls': {
    'api': 'https://api.example.com/data',  // string or dictionary of base API URLs
    'www': 'https://www.example.com'        // string website URL
    'doc': 'https://docs.example.com/api',  // string URL or array of URLs
    },
    'version':         'v1',            // string ending with digits
    'api':             { ... },         // dictionary of api endpoints
    'has': {                            // exchange capabilities
    'CORS': false,
    'publicAPI': true,
    'privateAPI': true,
    'cancelOrder': true,
    'createDepositAddress': false,
    'createOrder': true,
    'deposit': false,
    'fetchBalance': true,
    'fetchClosedOrders': false,
    'fetchCurrencies': false,
    'fetchDepositAddress': false,
    'fetchMarkets': true,
    'fetchMyTrades': false,
    'fetchOHLCV': false,
    'fetchOpenOrders': false,
    'fetchOrder': false,
    'fetchOrderBook': true,
    'fetchOrders': false,
    'fetchTicker': true,
    'fetchTickers': false,
    'fetchBidsAsks': false,
    'fetchTrades': true,
    'withdraw': false,
    },
    'timeframes': {                     // empty if the exchange !has.fetchOHLCV
    '1m': '1minute',
    '1h': '1hour',
    '1d': '1day',
    '1M': '1month',
    '1y': '1year',
    },
    'timeout':          10000,          // number in milliseconds
    'rateLimit':        2000,           // number in milliseconds
    'userAgent':       'ccxt/1.1.1 ...' // string, HTTP User-Agent header
    'verbose':          false,          // boolean, output error details
    'markets':         { ... }          // dictionary of markets/pairs by symbol
    'symbols':         [ ... ]          // sorted list of string symbols (traded pairs)
    'currencies':      { ... }          // dictionary of currencies by currency code
    'markets_by_id':   { ... },         // dictionary of dictionaries (markets) by id
    'proxy': 'https://crossorigin.me/', // string URL
    'apiKey':   '92560ffae9b8a0421...', // string public apiKey (ASCII, hex, Base64, ...)
    'secret':   '9aHjPmW+EtRRKN/Oi...'  // string private secret key
    'password': '6kszf4aci8r',          // string password
    'uid':      '123456',               // string user id
    }
     */




    
    /**
     * @return array
     */
    public function validate()
    {
        $errors = array();


        if (!$this->userId) {
            $errors['userId'] = 'Invalid value: userId';
        }

//        if (!$this->username) {
//            $errors['username'] = 'Invalid value: name';
//        }

        if (!$this->driver) {
            $errors['driver'] = 'Invalid value: driver';
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
