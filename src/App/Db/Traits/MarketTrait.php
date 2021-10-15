<?php

namespace App\Db\Traits;

use App\Db\Market;
use App\Db\MarketMap;
use Exception;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2019 Michael Mifsud
 */
trait MarketTrait
{

    /**
     * @var Market
     */
    private $_market = null;


    /**
     * @return int
     */
    public function getMarketId() : int
    {
        return $this->marketId;
    }

    /**
     * @param int $clientId
     * @return $this
     */
    public function setMarketId($clientId)
    {
        $this->marketId = (int)$clientId;
        return $this;
    }

    /**
     * Get the subject related to this object
     *
     * @return Market|null
     */
    public function getMarket()
    {
        if (!$this->_market) {
            try {
                $this->_market = MarketMap::create()->find($this->getMarketId());
            } catch (Exception $e) {
            }
        }
        return $this->_market;
    }

    /**
     * @param array $errors
     * @return array
     */
    public function validateMarketId($errors = [])
    {
        if (!$this->getMarketId()) {
            $errors['marketId'] = 'Invalid value: marketId';
        }
        return $errors;
    }

}