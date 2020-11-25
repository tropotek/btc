<?php
namespace App\Listener;

use App\Db\Candle;
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
class BotMovingAverageHandler implements Subscriber
{
    use ConfigTrait;


    /**
     * @param \App\Event\BotEvent $event
     * @throws \Exception
     */
    public function doExecute(\App\Event\BotEvent $event)
    {

        $event->set('ma.20', $this->getMa($event->getCandle(), 20, 'close'));
        $event->set('ma.100', $this->getMa($event->getCandle(), 100, 'close'));

    }


    /**
     * @param Candle $candle
     * @param int $length
     * @param string $source ['close', 'open', 'high', 'low']
     */
    protected function getMa(Candle $candle, $length = 20, $source = 'close')
    {
        $avg = 0;
        $objList = CandleMap::create()->findFiltered([
            'exchangeId' => $candle->getExchangeId(), 'symbol' => $candle->getSymbol(), 'period' => $candle->getPeriod(),
            //'dateStart' => \Tk\Date::create()->sub(new \DateInterval('P2D')),
            'dateEnd' => $candle->getTimestamp()
        ], Tool::create('', $length));
        if ($objList->countAll() < $length) return $avg;
        $list = $objList->toArray($source);
        $avg = array_sum($list)/count($list);
        return $avg;
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