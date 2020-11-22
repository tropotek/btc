<?php
namespace App\Console;

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
     * @var null|Exchange
     */
    protected $exchange = null;

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
        /** @var Exchange $exchangeObj */
        $this->exchange = $exchangeList = \App\Db\ExchangeMap::create()->find(1);


        $this->getTicker();

        //$this->getAllTrades();




    }


    /**
     * @throws \ccxt\NotSupported
     */
    protected function getTicker()
    {
        /** @var btcmarkets $exchange */
        $exchange = $this->exchange->getApi();

        //vd($exchange->fetchMarkets());
        //vd($exchange->fetchTickers());

        $exchange->loadMarkets();
        //vd(array_keys($exchange->markets));

        $data = $exchange->fetchTicker('BTC/AUD');
        $tick = Tick::create($this->exchange, $data);
        vd($data, $tick);


//        foreach(array_keys($exchange->markets) as $marketId) {
//            vd($exchange->fetchTicker($marketId));
//        }

//        //$arr2 = $exchange->fetchTrades('BTC/AUD', $exchange->milliseconds() - (86400000*5));
//        $arr = $exchange->fetchTrades('BTC/AUD');
//        vd($arr);
//
//        $a = current($arr);
//        vd($a);
//        vd(\Tk\Date::create($a['datetime']));
//        $a = $arr[1];
//        vd(\Tk\Date::create($a['datetime']));
//        $a = end($arr);
//        vd(\Tk\Date::create($a['datetime']));


//        $arr2 = $exchange->fetchOhlcv('BTC/AUD', '1M');
//
//        $a = current($arr2);
//        vd(\Tk\Date::create('@'.($a[0]/1000)));
//        $a = $arr2[1];
//        vd(\Tk\Date::create('@'.($a[0]/1000)));
//
//        $b = end($arr2);
//        vd(\Tk\Date::create('@'.($b[0]/1000)));



//        if ($exchange->has['fetchOHLCV']) {
//            foreach ($exchange->markets as $symbol => $market) {
//                usleep ($exchange->rateLimit * 1000); // usleep wants microseconds
//                vd ($exchange->fetch_ohlcv($symbol, '1d')); // one month
//            }
//        }
        //vd($exchange->has['fetchOHLCV']);
        //vd($exchange->fetchTickers($marketArray));

    }


    /**
     * @throws \ccxt\NotSupported
     */
    protected function getAllTrades()
    {
        $exchange = $this->exchange->getApi();

        $exchange->load_markets();
        $balance = $exchange->fetch_balance();
        $total = $balance['total'];
        $all_matching_symbols = array();
        foreach ($total as $currency_code => $value) {
            echo "-------------------------------------------------------------------\n";
            echo "Currency code: ", $currency_code, " value: ", $value, "\n";
            if ($value > 0) {
                // get all related markets with
                //   either base currency === currency code from the balance structure
                //      or quote currency === currency code from the balance structure
                $matching_markets = array_filter(array_values($exchange->markets), function ($market) use ($currency_code) {
                    return ($market['base'] === $currency_code) || ($market['quote'] === $currency_code);
                });
                $matching_symbols = $exchange->pluck ($matching_markets, 'symbol');
                echo "Matching symbols:\n";
                print_r($matching_symbols);
                $all_matching_symbols = array_merge ($all_matching_symbols, $matching_symbols);
            }
        }
        echo "========================================================================\n";
        $unique_symbols = $exchange->unique ($all_matching_symbols);
        print_r($unique_symbols);

        $all_trades_for_all_symbols = array();

        // ----------------------------------------------------------------------------
        function fetch_all_my_trades($exchange, $symbol) {
            $from_id = '0';
            $params = array('fromId' => $from_id);
            $previous_from_id = $from_id;
            $all_trades = array();
            while (true) {

                echo "------------------------------------------------------------------\n";
                echo "Fetching with params:\n";
                print_r($params);
                $trades = $exchange->fetch_my_trades($symbol, null, null, $params);
                echo "Fetched ", count($trades), ' ', $symbol, " trades\n";
                if (count($trades)) {
                    $last_trade = $trades[count($trades) - 1];
                    if ($last_trade['id'] == $previous_from_id) {
                        break;
                    } else {
                        $params['fromId'] = $last_trade['id'];
                        $previous_from_id = $last_trade['id'];
                        $all_trades = array_merge ($all_trades, $trades);
                    }
                } else {
                    break;
                }
            }

            echo "Fetched ", count($all_trades), ' ', $symbol, " trades\n";
            for ($i = 0; $i < count($all_trades); $i++) {
                $trade = $all_trades[$i];
                echo $i, ' ', $trade['symbol'], ' ', $trade['id'], ' ', $trade['datetime'], ' ', $trade['amount'], "\n";
            }
            return $all_trades;
        }

        // ----------------------------------------------------------------------------
        foreach ($unique_symbols as $symbol) {
            echo "=================================================================\n";
            echo "Fetching all ", $symbol, " trades\n";
            // fetch all trades for the $symbol, with pagination
            $trades = fetch_all_my_trades($exchange, $symbol);
            echo count($trades), ' ' , $symbol, " trades\n";
            $all_trades_for_all_symbols = array_merge ($all_trades_for_all_symbols, $trades);

        }
    }



}
