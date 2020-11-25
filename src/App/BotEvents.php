<?php
namespace App;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class BotEvents
{

    /**
     * @event \App\Event\BotEvent
     */
    const BOT_EXECUTE = 'bot.execute';

    /**
     * @event \App\Event\BotEvent
     */
    const BOT_BUY = 'bot.buy';

    /**
     * @event \App\Event\BotEvent
     */
    const BOT_SELL = 'bot.sell';

}