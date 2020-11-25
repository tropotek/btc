<?php
namespace App\Listener;

use App\Db\Candle;
use App\Db\CandleInterface;
use App\Db\CandleMap;
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

        list($rs, $rsi) = $this->getRsi($list);
        $event->set('rs.14', $rs);
        $event->set('rsi.14', $rsi);

//        list($rs, $rsi) = $this->getRsi($event->getCandle(), 28, 'close');
//        $event->set('rs.28', $rs);
//        $event->set('rsi.28', $rsi);

    }


    /**
     * @param float[] $list
     * @link https://school.stockcharts.com/doku.php?id=technical_indicators:relative_strength_index_rsi
     * @link https://www.macroption.com/rsi-calculation/
     * @link https://github.com/hurdad/doo-forex/blob/master/protected/class/Technical%20Indicators/RSI.php
     */
    public static function getRsi($list)
    {
        $length = count($list);
        $prev = 0;
        $rs = 0;
        $rsi = 100;
        $changeArray = [];

        foreach ($list as $i => $c) {
            // Need 2 points to get change
            if ($i >= 1) {
                $change = $c - $prev;
                //add to front
                array_unshift($changeArray, $change);
                //pop back if too long
                if (count($changeArray) > $length)
                    array_pop($changeArray);
            }
            $prev = $c;
        }

        //reduce change array getting sum loss and sum gains
        $res = array_reduce($changeArray, function ($result, $item) {
            if ($item >= 0)
                $result['sum_gain'] += $item;
            if ($item < 0)
                $result['sum_loss'] += abs($item);
            return $result;
        }, array('sum_gain' => 0, 'sum_loss' => 0));
        $avg_gain = $res['sum_gain'] / $length;
        $avg_loss = $res['sum_loss'] / $length;

        // Check divide by zero
        if ($avg_loss > 0) {
            //calc and normalize
            $rs = $avg_gain / $avg_loss;
            $rsi = 100 - (100 / (1 + $rs));
        }

        return [$rs, $rsi];
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