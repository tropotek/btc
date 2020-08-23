<?php
namespace App\Db;

use ccxt\ExchangeError;
use Tk\Db\Exception;

/**
 * @author Mick Mifsud
 * @created 2019-05-30
 * @link http://tropotek.com.au/
 * @license Copyright 2019 Tropotek
 */
class Exchange extends \Tk\Db\Map\Model implements \Tk\ValidInterface
{
    const MARKET_ALL = 'ALL';

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

    /**
     * @var string
     */
    public $driver = '\\App\\Driver\\BtcMarkets';

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
        array_shift($list);
        array_shift($list);
        //$list['Btcmarkets V3'] = '\\App\\Driver\\BtcMarkets3';
        ksort($list);
        return $list;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return ucfirst($this->driver);
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
            if (!class_exists($driver))
                $driver = '\\ccxt\\' . $this->driver;

            $this->_api = new $driver(array(
                'apiKey' => $this->apiKey,
                'secret' => $this->secret
                // TODO: add more options over time as needed
            ));
        }
        return $this->_api;
    }

    /**
     * Live Call
     * @param null|string $currency
     * @return array
     */
    public function getAccountSummary($currency = null)
    {
        if (!$currency)
            $currency = $this->getCurrency();

        // TODO: https://github.com/ccxt/ccxt/wiki/Manual


        $api = $this->getApi();
        $m = $api->loadMarkets();
        vdd(array_keys($m));
        $balance = $api->fetchBalance();
        $marketTotals = $balance['total'];
        $totals = array();

        foreach ($marketTotals as $coin => $amount) {
            if (strtoupper($coin) == strtoupper($currency)) continue;
            $marketId = strtoupper($coin) . '/' . strtoupper($currency);
            if (array_key_exists($marketId, $api->markets)) {
                //\Tk\Log::info($marketId);
                try {
                    $t = $api->fetchTicker($marketId);
                    //vd($t);
                    $totals[$coin] = 0;
                    //vd($coin, $t, $amount, \ccxt\Exchange::truncate($amount, 8), self::truncateToString($amount, 8));
                    if (self::truncateToString($amount, 8) > 0) {
                        $totals[$coin] = $t['bid'] * self::truncateToString($amount,8);       // I think this reflects a more accurate total
                        //$totals[$coin] = $t['ask'] * $amount;
                    }
                } catch (ExchangeError $e) {
                    \Tk\Log::error($marketId . ' ' . $e->getMessage());
                } catch (\Exception $e) {
                    \Tk\Log::error($e->__toString());
                }
            }
        }
        return $totals;
    }

    /**
     * Live Call
     * @param null $currency
     * @return float|int
     */
    public function getLiveTotalEquity($currency = null)
    {
        $marketTotals = $this->getAccountSummary($currency);
        return array_sum($marketTotals);
    }

    /**
     * @param null|string $currency
     * @return float|string
     */
    public function getTotalEquity($currency = null)
    {
        if (!$currency)
            $currency = $this->getCurrency();
        try {
            $obj = current(ExchangeMap::create()->findEquityTotals($this->getId(), self::MARKET_ALL, $currency,
                null, \Tk\Db\Tool::create('created DESC', 1)));
            if ($obj && !empty($obj->amount)) {
                return $obj->amount;
            }
        } catch (Exception $e) { }
        return 0;
    }

    /**
     * Live Call
     * @return float
     */
    public function getAvailableCurrency()
    {
        $api = $this->getApi();
        $api->loadMarkets();
        $balance = $api->fetchBalance();
        $totals = $balance['total'];
        if (isset($totals[$this->currency]))
            return $totals[$this->currency];
        return 0.0;
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
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return Exchange
     */
    public function setCurrency(string $currency)
    {
        $this->currency = $currency;
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


    private static $marketNames = [
        'AUD'   => 'Australian Dollar',
        'BTC'   => 'Bitcoin',
        'LTC' => 'Litecoin',
        'ETH' => 'Ethereum',
        'ETC' => 'Ethereum-Classic',
        'XRP' => 'Ripple',
        'OMG' => 'OmiseGO',
        'POWR' => 'Power Ledger',
        'BCHABC' => 'BCHABC',
        'BCHSV' => 'BCHSV',
        'BAT' => 'Basic Attention',
        'GNT' => 'Golem',
        'XLM' => 'Stellar Lumens',
        'ENJ' => 'Enjin Coin',
        'LINK' => 'Chainlink',
        'BSV' => 'Bitcoin SV'
    ];

    /**
     * Return the full market name if known
     *
     * @param string $market
     * @return mixed
     */
    public function getMarketName($market)
    {
        if (array_key_exists($market, self::$marketNames))
            return self::$marketNames[$market];
        return $market;
    }

    public static function truncateToString($number, $precision = 0)
    {
        return \ccxt\Exchange::truncate_to_string($number, $precision);
    }

    public static function toFloat($number, $precision = 4)
    {
        return sprintf('%.'.$precision.'f', self::truncateToString($number));
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

//        if (!$this->username) {
//            $errors['username'] = 'Invalid value: name';
//        }

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
