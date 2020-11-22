<?php
namespace App\Db;

use App\Db\Traits\ExchangeTrait;

/**
 * @author Mick Mifsud
 * @created 2020-11-22
 * @link http://tropotek.com.au/
 * @license Copyright 2020 Tropotek
 */
class Tick extends \Tk\Db\Map\Model implements \Tk\ValidInterface
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
     * @var int
     */
    public $timestamp = 0;

    /**
     * @var \DateTime
     */
    public $datetime = null;

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
    public $bid = 0;

    /**
     * @var float
     */
    public $ask = 0;

    /**
     * @var float
     */
    public $last = 0;

    /**
     * @var float
     */
    public $close = 0;

    /**
     * @var float
     */
    public $change = 0;

    /**
     * @var float
     */
    public $percentage = 0;


    /**
     * Tick
     */
    public function __construct()
    {
        $this->timestamp = new \DateTime();
        $this->datetime = new \DateTime();
    }

    /**
     * @param Exchange $exchange
     * @param array $data
     * @return Tick
     */
    public static function create(Exchange $exchange, array $data)
    {
        $tick = new self();
        $tick->setExchangeId($exchange->getId());
        TickMap::create()->map($data, $tick);
        return $tick;
    }


    /**
     * @param string $symbol
     * @return Tick
     */
    public function setSymbol($symbol) : Tick
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
     * @param int $timestamp
     * @return Tick
     */
    public function setTimestamp($timestamp) : Tick
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param \DateTime $datetime
     * @return Tick
     */
    public function setDatetime($datetime) : Tick
    {
        $this->datetime = $datetime;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDatetime() : \DateTime
    {
        return $this->datetime;
    }

    /**
     * @param float $high
     * @return Tick
     */
    public function setHigh($high) : Tick
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
     * @return Tick
     */
    public function setLow($low) : Tick
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
     * @param float $bid
     * @return Tick
     */
    public function setBid($bid) : Tick
    {
        $this->bid = $bid;
        return $this;
    }

    /**
     * @return float
     */
    public function getBid() : float
    {
        return $this->bid;
    }

    /**
     * @param float $ask
     * @return Tick
     */
    public function setAsk($ask) : Tick
    {
        $this->ask = $ask;
        return $this;
    }

    /**
     * @return float
     */
    public function getAsk() : float
    {
        return $this->ask;
    }

    /**
     * @param float $last
     * @return Tick
     */
    public function setLast($last) : Tick
    {
        $this->last = $last;
        return $this;
    }

    /**
     * @return float
     */
    public function getLast() : float
    {
        return $this->last;
    }

    /**
     * @param float $close
     * @return Tick
     */
    public function setClose($close) : Tick
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
     * @param float $change
     * @return Tick
     */
    public function setChange($change) : Tick
    {
        $this->change = $change;
        return $this;
    }

    /**
     * @return float
     */
    public function getChange() : float
    {
        return $this->change;
    }

    /**
     * @param float $percentage
     * @return Tick
     */
    public function setPercentage($percentage) : Tick
    {
        $this->percentage = $percentage;
        return $this;
    }

    /**
     * @return float
     */
    public function getPercentage() : float
    {
        return $this->percentage;
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

        return $errors;
    }

}
