<?php
/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
namespace App;


use App\Db\Candle;
use App\Event\BotEvent;
use Tk\ConfigTrait;

class Bot
{
    use ConfigTrait;


    /**
     * @var BotEvent
     */
    protected $event = null;


    public function execute(Candle $candle)
    {
        $this->event = new BotEvent($candle);
        if (!$this->getConfig()->getEventDispatcher()) return;

        $this->getConfig()->getEventDispatcher()->dispatch(BotEvents::BOT_EXECUTE, $this->getEvent());

        if ($this->getEvent()->getBuy() > 0) {
            $this->getConfig()->getEventDispatcher()->dispatch(BotEvents::BOT_BUY, $this->getEvent());
        }

        if ($this->getEvent()->getSell() > 0) {
            $this->getConfig()->getEventDispatcher()->dispatch(BotEvents::BOT_SELL, $this->getEvent());
        }
    }


    /**
     * @return BotEvent|null
     */
    public function getEvent()
    {
        return $this->event;
    }

}