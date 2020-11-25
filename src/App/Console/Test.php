<?php
namespace App\Console;

use App\Bot;
use App\Db\Candle;
use App\Db\CandleMap;
use App\Db\Exchange;
use App\Db\Tick;
use App\Db\TickMap;
use ccxt\btcmarkets;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2017 Michael Mifsud
 */
class Test extends \Bs\Console\Iface
{




    /**
     *
     */
    protected function configure()
    {
        $this->setName('test')
            ->setDescription('This is a test script only');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        // required vars
        $config = \App\Config::getInstance();
        if (!$config->isDebug()) {
            $this->writeError('Error: Only run this command in a debug environment.');
            return;
        }
        /** @var Exchange| $exchange */
        $exchange = \App\Db\ExchangeMap::create()->find(1);

        $this->testBot($exchange);
        //$this->saveCandles($exchange);

    }




    protected function testBot(\App\Db\Exchange $exchange)
    {
        $list = CandleMap::create()->findFiltered([
            'exchangeId' => $exchange->getId(), 'symbol' => 'XRP/'.$exchange->getCurrency(), 'period' => 'm',
            //'dateStart' => \Tk\Date::create("now", new \DateTimeZone("UTC"))->sub(new \DateInterval('PT1H')),
            'dateEnd' => \Tk\Date::create("now", new \DateTimeZone("UTC"))
        ]);

        $bot = new Bot();
        foreach ($list as $candle) {
            $bot->execute($candle);
            vd($candle->getSymbol(), $candle->getTimestamp(), $bot->getEvent()->getCollection());
        }

    }






    /**
     *
     * (s=sec, m=minute, h=hour, d=day, w=week, M=month, y=year)
     *
     *        1504541580000, // UTC timestamp in milliseconds, integer
     *        4235.4,        // (O)pen price, float
     *        4240.6,        // (H)ighest price, float
     *        4230.0,        // (L)owest price, float
     *        4230.7,        // (C)losing price, float
     *        37.72941911    // (V)olume (in terms of the base currency), float
     *
     *
     * @param \App\Db\Exchange $exchange
     * @throws \ccxt\NotSupported
     */
    protected function saveCandles(\App\Db\Exchange $exchange)
    {
        //$periods = ['s', 'm', 'h', 'd', 'w', 'M', 'y'];
        $periods = ['m', 'h', 'd'];
        //$periods = ['m'];

        /** @var btcmarkets $api */
        $api = $exchange->getApi();
        if (!$api->has['fetchOHLCV']) return;
        $api->loadMarkets();
        foreach($api->markets as $symbol => $market) {
            foreach ($periods as $period) {
                usleep ($api->rateLimit * 1000);
                $ohlcv = $api->fetch_ohlcv($symbol, '1'.$period);
                foreach ($ohlcv as $i => $data) {
                    // Note: that the info from the last (current) candle may be incomplete until
                    //       the candle is closed (until the next candle starts).
                    if ($i == count($ohlcv)-1) continue;    // do not save the last candle
                    $found = CandleMap::create()->findFiltered([
                        'exchangeId' => $exchange->getId(), 'symbol' => $symbol, 'period' => $period, 'timestamp' => ($data[0]/1000)
                    ])->current();
                    if (!$found) {
                        $candle = Candle::create($exchange, $symbol, $period, $data);
                        vd($candle);
                        $candle->save();
                    }
                }
            }
        }
    }





}
