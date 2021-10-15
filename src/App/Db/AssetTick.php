<?php
namespace App\Db;

use App\Db\Traits\AssetTrait;
use Bs\Db\Traits\CreatedTrait;

/**
 * @author Mick Mifsud
 * @created 2021-10-15
 * @link http://tropotek.com.au/
 * @license Copyright 2021 Tropotek
 */
class AssetTick extends \Tk\Db\Map\Model implements \Tk\ValidInterface
{
    use AssetTrait;
    use CreatedTrait;

    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var int
     */
    public $assetId = 0;

    /**
     * @var float
     */
    public $units = 0;

    /**
     * @var string
     */
    public $currency = 'AUD';

    /**
     * @var float
     */
    public $bid = 0;

    /**
     * @var float
     */
    public $ask = 0;

    /**
     * @var \DateTime
     */
    public $created = null;


    /**
     * AssetTick
     */
    public function __construct()
    {
        $this->_CreatedTrait();

    }

    /**
     * @throws \Exception
     */
    public static function updateAssetTicks()
    {
        $currency = 'AUD';
        $list = AssetMap::create()->findFiltered(['active' => true]);
        foreach ($list as $asset) {
            if (!$asset->getMarket()->getExchange()) continue;
            try {
                $api = $asset->getMarket()->getExchange()->getApi();
                $api->loadMarkets();
                usleep($api->rateLimit * 1000); // usleep wants microseconds
                $marketId = $asset->getMarket()->getSymbol() . '/' . $currency;
                $data = $api->fetchTicker($marketId);
                if (count($data)) {
                    $tick = new AssetTick();
                    $tick->setAssetId($asset->getId());
                    $tick->setUnits($asset->getUnits());
                    $tick->setCurrency($currency);
                    $tick->setBid($data['bid']);
                    $tick->setAsk($data['ask']);
                    $tick->save();
                }
            } catch (\Exception $e) { continue; }
        }
    }

    /**
     * @param float $units
     * @return AssetTick
     */
    public function setUnits($units) : AssetTick
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
     * @param string $currency
     * @return AssetTick
     */
    public function setCurrency($currency) : AssetTick
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency() : string
    {
        return $this->currency;
    }

    /**
     * @param float $bid
     * @return AssetTick
     */
    public function setBid($bid) : AssetTick
    {
        $this->bid = $bid;
        return $this;
    }

    /**
     * @return float
     */
    public function getBid() : float
    {
        return $this->bid;
    }

    /**
     * @param float $ask
     * @return AssetTick
     */
    public function setAsk($ask) : AssetTick
    {
        $this->ask = $ask;
        return $this;
    }

    /**
     * @return float
     */
    public function getAsk() : float
    {
        return $this->ask;
    }


    /**
     * @return array
     */
    public function validate()
    {
        $errors = array();

        if (!$this->assetId) {
            $errors['assetId'] = 'Invalid value: assetId';
        }

        if (!$this->currency) {
            $errors['currency'] = 'Invalid value: currency';
        }

        return $errors;
    }

}
