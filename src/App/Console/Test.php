<?php
namespace App\Console;

use App\Bot;
use App\Db\Asset;
use App\Db\AssetMap;
use App\Db\AssetTick;
use App\Db\Candle;
use App\Db\CandleMap;
use App\Db\Exchange;
use App\Db\Tick;
use App\Db\TickMap;
use Bs\Db\User;
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

        //AssetTick::updateAssetTicks();

        $list = $this->getConfig()->getUserMapper()->findFiltered(['type' => User::TYPE_MEMBER]);
        foreach ($list as $user) {
            Asset::updateAssetTotalTick($user);
        }

    }






}
