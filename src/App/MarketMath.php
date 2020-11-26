<?php


namespace App;


class MarketMath
{

    /**
     * @param float[] $list
     * @link https://school.stockcharts.com/doku.php?id=technical_indicators:relative_strength_index_rsi
     * @link https://www.macroption.com/rsi-calculation/
     * @link https://github.com/hurdad/doo-forex/blob/master/protected/class/Technical%20Indicators/RSI.php
     * @todo: Check and validate teh values of this function
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
}