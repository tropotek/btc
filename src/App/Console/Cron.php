<?php
namespace App\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

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
        parent::execute($input, $output);
        //$output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);
        vd(\Tk\Date::create()->format(\Tk\Date::FORMAT_ISO_DATETIME));

        $exchangeList = \App\Db\ExchangeMap::create()->findFiltered(array(
            'active' => true
        ));
        foreach ($exchangeList as $exchange) {
            $this->processExchange($exchange);
        }

        $this->write('Cron Script Executed...', OutputInterface::VERBOSITY_VERBOSE);
    }

    /**
     * @param \App\Db\Exchange $exchange
     */
    public function processExchange(\App\Db\Exchange $exchange)
    {
        $api = $exchange->getExchangeObj();
        $api->loadMarkets();

        //$api->loadMarkets();
        $balance = $api->fetchBalance();

        $currency = 'AUD';
        $marketTotals = $balance['total'];
        $totals = array();
        foreach ($marketTotals as $coin => $amount) {
            $marketId = strtoupper($coin) . '/' . strtoupper($currency);
            if (!array_key_exists($marketId, $api->markets)) {
                continue;
            }
            //$market = $api->market($marketId);
            //$t = $api->fetchTrades($marketId, microtime(true)-100000);
            //$t = $api->markets[$marketId];
            //$t = $api->fetchOrderBook($marketId, 2);
            $t = $api->fetchTicker($marketId);

            //$v1 = \ccxt\Exchange::decimalToPrecision($amount);
            $v2 = \ccxt\Exchange::number_to_string($amount);

            vd($t, $amount, $v2);


        }


        vd($totals);

    }

}
