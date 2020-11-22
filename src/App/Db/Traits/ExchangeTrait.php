<?php

namespace App\Db\Traits;

use App\Db\Exchange;
use App\Db\ExchangeMap;
use Exception;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2019 Michael Mifsud
 */
trait ExchangeTrait
{

    /**
     * @var Exchange
     */
    private $_exchange = null;


    /**
     * @return int
     */
    public function getExchangeId()
    {
        return $this->exchangeId;
    }

    /**
     * @param int $clientId
     * @return $this
     */
    public function setExchangeId($clientId)
    {
        $this->exchangeId = (int)$clientId;
        return $this;
    }

    /**
     * Get the subject related to this object
     *
     * @return Exchange|null
     */
    public function getExchange()
    {
        if (!$this->_exchange) {
            try {
                $this->_exchange = ExchangeMap::create()->find($this->getExchangeId());
            } catch (Exception $e) {
            }
        }
        return $this->_exchange;
    }

    /**
     * @param array $errors
     * @return array
     */
    public function validateExchangeId($errors = [])
    {
        if (!$this->getExchangeId()) {
            $errors['exchangeId'] = 'Invalid value: exchangeId';
        }
        return $errors;
    }

}