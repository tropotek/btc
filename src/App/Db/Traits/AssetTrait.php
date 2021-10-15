<?php

namespace App\Db\Traits;

use App\Db\Asset;
use App\Db\AssetMap;
use Exception;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2019 Michael Mifsud
 */
trait AssetTrait
{

    /**
     * @var Asset
     */
    private $_asset = null;


    /**
     * @return int
     */
    public function getAssetId() : int
    {
        return $this->assetId;
    }

    /**
     * @param int $clientId
     * @return $this
     */
    public function setAssetId($clientId)
    {
        $this->assetId = (int)$clientId;
        return $this;
    }

    /**
     * Get the subject related to this object
     *
     * @return Asset|null
     */
    public function getAsset()
    {
        if (!$this->_asset) {
            try {
                $this->_asset = AssetMap::create()->find($this->getAssetId());
            } catch (Exception $e) {
            }
        }
        return $this->_asset;
    }

    /**
     * @param array $errors
     * @return array
     */
    public function validateAssetId($errors = [])
    {
        if (!$this->getAssetId()) {
            $errors['assetId'] = 'Invalid value: assetId';
        }
        return $errors;
    }

}