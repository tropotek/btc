<?php
namespace App\Db;

use App\Db\Traits\MarketTrait;
use Bs\Db\Traits\TimestampTrait;
use Bs\Db\Traits\UserTrait;
use Tk\Db\Tool;

/**
 * @author Mick Mifsud
 * @created 2021-10-15
 * @link http://tropotek.com.au/
 * @license Copyright 2021 Tropotek
 */
class Asset extends \Tk\Db\Map\Model implements \Tk\ValidInterface
{
    use UserTrait;
    use MarketTrait;
    use TimestampTrait;

    const MARKET_BID = 'bid';
    const MARKET_ASK = 'ask';

    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var int
     */
    public $marketId = 0;

    /**
     * @var int
     */
    public $userId = 0;

    /**
     * @var int
     * @todo allow for collections of assets
     */
    public $categoryId = 0;

    /**
     * @var float
     */
    public $units = 0;

    /**
     * @var string
     */
    public $notes = '';

    /**
     * @var \DateTime
     */
    public $modified = null;

    /**
     * @var \DateTime
     */
    public $created = null;

    /**
     * @var null|AssetTick
     */
    private $_tick = null;


    /**
     * Asset
     */
    public function __construct()
    {
        $this->_TimestampTrait();
        if ($this->getAuthUser())
            $this->setUserId($this->getAuthUser()->getId());
    }

    /**
     * @return int
     * @throws \Tk\Db\Exception
     */
    public function delete()
    {
        AssetTickMap::create()->deleteTicksByAssetId($this->getVolatileId());
        return parent::delete();
    }

    /**
     * @return AssetTick|object|\Tk\Db\Map\Model|null
     * @throws \Exception
     */
    public function getAssetTick()
    {
        $cur = 'AUD';
        if ($this->getMarket() && $this->getMarket()->getExchange()) {
            $cur = $this->getMarket()->getExchange()->getCurrency();
        }
        if (!$this->_tick) {
            $this->_tick = AssetTickMap::create()->findFiltered(['assetId' => $this->getId(), 'currency' => $cur], Tool::create('created DESC', 1))->current();
        }
        return $this->_tick;
    }

    /**
     * @param string $returnType
     * @return float|int
     * @throws \Exception
     */
    public function getMarketUnitValue($returnType = 'bid')
    {
        $val = 0;
        $tick = $this->getAssetTick();
        if ($tick) {
            if ($returnType == self::MARKET_BID)
                $val = $tick->getBid();
            else if ($returnType == self::MARKET_ASK)
                $val = $tick->getAsk();
        }
        return $val;
    }

    /**
     * @param string $returnType
     * @return float|int
     * @throws \Exception
     */
    public function getMarketTotalValue($returnType = 'bid')
    {
        $val = 0;
        $tick = $this->getAssetTick();
        if ($tick) {
            if ($returnType == self::MARKET_BID)
                $val = $tick->getBid() * $this->getUnits();
            else if ($returnType == self::MARKET_ASK)
                $val = $tick->getAsk() * $this->getUnits();
        }
        return $val;
    }

    /**
     * @param int $limit
     * @param string $returnType
     * @return array
     * @throws \Exception
     */
    public function getMarketHistory($limit = 10, $returnType = 'bid')
    {
        $src = AssetTickMap::create()->findFiltered(['assetId' => $this->getId()], Tool::create('created DESC', $limit));
        $dst = [];
        foreach ($src as $tick) {
            if ($returnType == self::MARKET_BID)
                $dst[$tick->getCreated()->getTimestamp()] = round($tick->getBid(), 2);
            else if ($returnType == self::MARKET_ASK)
                $dst[$tick->getCreated()->getTimestamp()] = round($tick->getAsk(), 2);
        }
        $dst = array_reverse($dst);
        return $dst;
    }

    /**
     * @param int $limit
     * @param string $currency
     * @param string $returnType
     * @return array
     * @throws \Exception
     */
    public function getAssetTotalHistory($limit = 10, $returnType = 'bid')
    {
        $src = AssetTickMap::create()->findFiltered(['assetId' => $this->getId()], Tool::create('created DESC', $limit));
        $dst = [];
        foreach ($src as $tick) {
            if ($returnType == self::MARKET_BID)
                $dst[$tick->getCreated()->getTimestamp()] = round($tick->getUnits() * $tick->getBid(), 2);
            else if ($returnType == self::MARKET_ASK)
                $dst[$tick->getCreated()->getTimestamp()] = round($tick->getUnits() * $tick->getAsk(), 2);
        }
        return $dst;
    }


    /**
     * @return string
     */
    public function getSymbol() : string
    {
        $str = '';
        if ($this->getMarket()) {
            $str = $this->getMarket()->getSymbol();
        }
        return $str;
    }

    /**
     * @param float $units
     * @return Asset
     */
    public function setUnits($units) : Asset
    {
        $this->units = $units;
        return $this;
    }

    /**
     * @return float
     */
    public function getUnits() : float
    {
        return $this->units;
    }

    /**
     * @param string $notes
     * @return Asset
     */
    public function setNotes($notes) : Asset
    {
        $this->notes = $notes;
        return $this;
    }

    /**
     * @return string
     */
    public function getNotes() : string
    {
        return $this->notes;
    }


    /**
     * @return array
     */
    public function validate()
    {
        $errors = array();

        if (!$this->userId) {
            $errors['userId'] = 'Invalid value: userId';
        }

        if (!$this->marketId) {
            $errors['marketId'] = 'Invalid value: marketId';
        }

        return $errors;
    }

}
