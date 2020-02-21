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
        $this->setInput($input);
        $this->setOutput($output);

        $exchangeList = \App\Db\ExchangeMap::create()->findFiltered(array(
            'active' => true
        ));
        foreach ($exchangeList as $exchange) {
            $this->processExchange($exchange);
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
