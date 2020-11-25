<?php
namespace App\Console;

use App\Db\Candle;
use App\Db\CandleMap;
use App\Db\Tick;
use ccxt\btcmarkets;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Tk\Db\Data;

/**
 * Cron job to be run nightly
 *
 * # run Nightly site cron job
 *   * /5  *  *   *   *      php /home/user/public_html/bin/cmd cron > /dev/null 2>&1
 *
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2017 Michael Mifsud
 */
class Cron extends \Bs\Console\Iface
{

    /**
     *
     */
    protected function configure()
    {
        $path = getcwd();
        $this->setName('cron')
            ->setDescription('The site cron script. crontab line: */5 *  * * *   '.$path.'/bin/cmd cron > /dev/null 2>&1');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setInput($input);
        $this->setOutput($output);


        $data = Data::create();
        $now = \Tk\Date::create();
        $lastHour = $data->get('cron.last.hour', null);
        $lastDay = $data->get('cron.last.day', null);
        $lastWeek = $data->get('cron.last.week', null);

        // Instant Jobs
        $this->execNow();

        // Hourly Jobs
        if (!$lastHour || $now->sub(new \DateInterval('PT1H')) >= $lastHour) {
            $data->set('cron.last.hour', $now)->save();
            $this->writeComment('Cron Hourly Executed:');
            \Tk\Log::alert('HOURLY CRON JOB EXECUTE()');
            $this->execHour();

        }

        // Daily Jobs
        if (!$lastDay || $now->sub(new \DateInterval('P1D')) >= $lastDay) {
            $data->set('cron.last.day', $now)->save();
            $this->writeComment('Cron Daily Executed:');
            \Tk\Log::alert('DAILY CRON JOB EXECUTE()');
            $this->execDay();

        }

        // Weekly Jobs
        if (!$lastWeek || $now->sub(new \DateInterval('P1W')) >= $lastWeek) {
            $data->set('cron.last.week', $now)->save();
            $this->writeComment('Cron Weekly Executed:');
            \Tk\Log::alert('WEEKLY CRON JOB EXECUTE()');
            $this->execWeek();
        }


    }


    protected function execNow()
    {
        try {
            $exchangeList = \App\Db\ExchangeMap::create()->findFiltered(['active' => true]);
            foreach ($exchangeList as $exchange) {
                $this->saveTickers($exchange);
                $this->saveCandles($exchange, ['m']);

                $this->processExchange($exchange);
            }
        } catch (\Exception $e) {
            vd($e->__toString());
        }
    }

    protected function execHour()
    {
        try {
            $exchangeList = \App\Db\ExchangeMap::create()->findFiltered(['active' => true]);
            foreach ($exchangeList as $exchange) {
                $this->saveCandles($exchange, ['h']);
            }
        } catch (\Exception $e) {
            vd($e->__toString());
        }

    }

    protected function execDay()
    {
        try {
            $exchangeList = \App\Db\ExchangeMap::create()->findFiltered(['active' => true]);
            foreach ($exchangeList as $exchange) {
                $this->saveCandles($exchange, ['d']);
            }
        } catch (\Exception $e) {
            vd($e->__toString());
        }
    }

    protected function execWeek()
    {

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
    protected function saveCandles(\App\Db\Exchange $exchange, $periods = ['m'])
    {
        //$periods = ['s', 'm', 'h', 'd', 'w', 'M', 'y'];
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
                        'exchangeId' => $exchange->getId(), 'symbol' => $symbol, 'period' => $period, 'timestamp' => (int)($data[0]/1000)
                    ])->current();
                    if (!$found) {
                        $candle = Candle::create($exchange, $symbol, $period, $data);
                        \Tk\Log::notice(' - ' . $symbol . ', 1' . $period . ', ' . (int)($data[0]/1000));
                        $candle->save();
                    }
                }
            }
        }
    }




    /**
     * @param \App\Db\Exchange $exchange
     * @throws \ccxt\NotSupported
     */
    protected function saveTickers(\App\Db\Exchange $exchange)
    {
        /** @var btcmarkets $api */
        $api = $exchange->getApi();
        $api->loadMarkets();
        foreach(array_keys($api->markets) as $marketId) {
            $data = $api->fetchTicker($marketId);
            $tick = Tick::create($exchange, $data);
            $tick->save();
        }
    }




    /**
     * @param \App\Db\Exchange $exchange
     * @throws \Tk\Db\Exception
     */
    public function processExchange(\App\Db\Exchange $exchange)
    {
        // Save total equity values
        $eq = $exchange->getLiveTotalEquity();
        \App\Db\ExchangeMap::create()->addEquityTotal($exchange->getId(), \App\Db\Exchange::MARKET_ALL, $exchange->getCurrency(), $eq);

        // Save individual coin equities
        $summaryList = $exchange->getAccountSummary();
        foreach ($summaryList as $market => $val) {
            if (\App\Db\Exchange::toFloat($val ,8) <= 0) continue;
            \App\Db\ExchangeMap::create()->addEquityTotal($exchange->getId(), $market, $exchange->getCurrency(), $val);
        }

        $this->write('Total Equity: ' . $eq . ' ' . $exchange->getCurrency());
        $avail = $exchange->getAvailableCurrency();
        $this->write('Available Currency: ' .  \App\Db\Exchange::toFloat($avail) . ' ' . $exchange->getCurrency() );


    }

}
