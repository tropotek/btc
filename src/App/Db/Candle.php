<?php
namespace App\Db;

use App\Db\Traits\ExchangeTrait;

/**
 * @author Mick Mifsud
 * @created 2020-11-25
 * @link http://tropotek.com.au/
 * @license Copyright 2020 Tropotek
 */
class Candle extends \Tk\Db\Map\Model implements \Tk\ValidInterface, CandleInterface
{
    use ExchangeTrait;

    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var int
     */
    public $exchangeId = 0;

    /**
     * @var string
     */
    public $symbol = '';

    /**
     * @var string
     */
    public $period = '';

    /**
     * @var int
     */
    public $timestamp = 0;

    /**
     * @var float
     */
    public $open = 0;

    /**
     * @var float
     */
    public $high = 0;

    /**
     * @var float
     */
    public $low = 0;

    /**
     * @var float
     */
    public $close = 0;

    /**
     * @var float
     */
    public $volume = 0;


    /**
     * Candle
     */
    public function __construct()
    {


    }

    /**
     * Expected $data Format:
     *  [
     *   1504541580000, // UTC timestamp in milliseconds, integer
     *   4235.4,        // (O)pen price, float
     *   4240.6,        // (H)ighest price, float
     *   4230.0,        // (L)owest price, float
     *   4230.7,        // (C)losing price, float
     *   37.72941911    // (V)olume (in terms of the base currency), float
     *  ]
     *
     * @param Exchange $exchange
     * @param string $symbol 'BTC', 'XLM', etc...
     * @param string $period ['s', 'm', 'h', 'd', 'w', 'M', 'y']
     * @param array $data
     * @return Candle
     * @throws \Exception
     */
    public static function create(Exchange $exchange, $symbol, $period, array $data)
    {
        $candle = new self();
        $candle->setExchangeId($exchange->getId());
        $candle->setSymbol($symbol);
        $candle->setPeriod($period);
        $candle->setTimestamp((int)($data[0]/1000));
        $candle->setOpen($data[1]);
        $candle->setHigh($data[2]);
        $candle->setLow($data[3]);
        $candle->setClose($data[4]);
        $candle->setVolume($data[5]);
        return $candle;
    }


    /**
     * @param string $symbol
     * @return Candle
     */
    public function setSymbol($symbol) : Candle
    {
        $this->symbol = $symbol;
        return $this;
    }

    /**
     * @return string
     */
    public function getSymbol() : string
    {
        return $this->symbol;
    }

    /**
     * @param string $period
     * @return Candle
     */
    public function setPeriod($period) : Candle
    {
        $this->period = $period;
        return $this;
    }

    /**
     * @return string
     */
    public function getPeriod() : string
    {
        return $this->period;
    }

    /**
     * @param int $timestamp
     * @return Candle
     */
    public function setTimestamp(int $timestamp) : Candle
    {
        $this->timestamp = (int)$timestamp;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimestamp() : int
    {
        return $this->timestamp;
    }

    /**
     * Return the timestamp as a \DadeTime object
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return \Tk\Date::create($this->getTimestamp());
    }

    /**
     * @param float $open
     * @return Candle
     */
    public function setOpen($open) : Candle
    {
        $this->open = $open;
        return $this;
    }

    /**
     * @return float
     */
    public function getOpen() : float
    {
        return $this->open;
    }

    /**
     * @param float $high
     * @return Candle
     */
    public function setHigh($high) : Candle
    {
        $this->high = $high;
        return $this;
    }

    /**
     * @return float
     */
    public function getHigh() : float
    {
        return $this->high;
    }

    /**
     * @param float $low
     * @return Candle
     */
    public function setLow($low) : Candle
    {
        $this->low = $low;
        return $this;
    }

    /**
     * @return float
     */
    public function getLow() : float
    {
        return $this->low;
    }

    /**
     * @param float $close
     * @return Candle
     */
    public function setClose($close) : Candle
    {
        $this->close = $close;
        return $this;
    }

    /**
     * @return float
     */
    public function getClose() : float
    {
        return $this->close;
    }

    /**
     * @param float $volume
     * @return Candle
     */
    public function setVolume($volume) : Candle
    {
        $this->volume = $volume;
        return $this;
    }

    /**
     * @return float
     */
    public function getVolume() : float
    {
        return $this->volume;
    }

    /**
     * @return array
     */
    public function validate()
    {
        $errors = array();

        if (!$this->exchangeId) {
            $errors['exchangeId'] = 'Invalid value: exchangeId';
        }

        if (!$this->symbol) {
            $errors['symbol'] = 'Invalid value: symbol';
        }

        if (!$this->period) {
            $errors['period'] = 'Invalid value: period';
        }

        return $errors;
    }

}
