<?php
namespace App\Event;

use App\Db\Candle;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class BotEvent extends \Tk\Event\Event
{

    /**
     * @var Candle
     */
    protected $candle = null;

    /**
     * @var float
     */
    protected $buy = 0.0;

    /**
     * @var float
     */
    protected $sell = 0.0;



    /**
     * constructor.
     *
     * @param Candle $status
     */
    public function __construct($status)
    {
        $this->candle = $status;
    }

    /**
     * @return Candle
     */
    public function getCandle()
    {
        return $this->candle;
    }

    /**
     * @return float
     */
    public function getBuy(): float
    {
        return $this->buy;
    }

    /**
     * valid values 0.0-1.0
     *
     * @param float $buy
     * @return BotEvent
     */
    public function setBuy(float $buy): BotEvent
    {
        $this->buy = $buy;
        return $this;
    }

    /**
     * @return float
     */
    public function getSell(): float
    {
        return $this->sell;
    }

    /**
     * valid values 0.0-1.0
     *
     * @param float $sell
     * @return BotEvent
     */
    public function setSell(float $sell): BotEvent
    {
        $this->sell = $sell;
        return $this;
    }

}