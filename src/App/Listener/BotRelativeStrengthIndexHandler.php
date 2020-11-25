<?php
namespace App\Listener;

use App\Db\Candle;
use App\Db\CandleInterface;
use App\Db\CandleMap;
use App\MarketMath;
use Symfony\Component\HttpKernel\KernelEvents;
use Tk\ConfigTrait;
use Tk\Db\Tool;
use Tk\Event\Subscriber;

/**
 * This object helps cleanup the structure of the controller code
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class BotRelativeStrengthIndexHandler implements Subscriber
{
    use ConfigTrait;


    /**
     * @param \App\Event\BotEvent $event
     * @throws \Exception
     */
    public function doExecute(\App\Event\BotEvent $event)
    {
        $length = 14;

        $list = CandleMap::create()->findFiltered([
            'exchangeId' => $event->getCandle()->getExchangeId(),
            'symbol' => $event->getCandle()->getSymbol(),
            'period' => $event->getCandle()->getPeriod(),
            //'dateStart' => \Tk\Date::create()->sub(new \DateInterval('P2D')),
            'dateEnd' => $event->getCandle()->getTimestamp()
        ], Tool::create('timestamp DESC', $length))->toArray('close');

        list($rs, $rsi) = MarketMath::getRsi($list);
        $event->set('rs.14', $rs);
        $event->set('rsi.14', $rsi);

//        list($rs, $rsi) = MarketMath::getRsi($event->getCandle(), 28, 'close');
//        $event->set('rs.28', $rs);
//        $event->set('rsi.28', $rsi);

    }




    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            \App\BotEvents::BOT_EXECUTE => array('doExecute', 0)
        );
    }
}