<?php


namespace App\Db;


interface CandleInterface
{

    /**
     * @return int
     */
    public function getExchangeId() : int;

    /**
     * @return string
     */
    public function getSymbol() : string;

    /**
     * @return string
     */
    public function getPeriod() : string;

    /**
     * @return int
     */
    public function getTimestamp() : int;

    /**
     * @return float
     */
    public function getOpen() : float;

    /**
     * @return float
     */
    public function getClose() : float;

    /**
     * @return float
     */
    public function getHigh() : float;

    /**
     * @return float
     */
    public function getLow() : float;

    /**
     * @return float
     */
    public function getVolume() : float;

}