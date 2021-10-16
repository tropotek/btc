<?php
namespace App\Db;

use Tk\Db\Tool;
use Tk\Db\Map\ArrayObject;
use Tk\DataMap\Db;
use Tk\DataMap\Form;
use Bs\Db\Mapper;
use Tk\Db\Filter;

/**
 * @author Mick Mifsud
 * @created 2021-10-15
 * @link http://tropotek.com.au/
 * @license Copyright 2021 Tropotek
 */
class AssetMap extends Mapper
{

    /**
     * @return \Tk\DataMap\DataMap
     */
    public function getDbMap()
    {
        if (!$this->dbMap) { 
            $this->dbMap = new \Tk\DataMap\DataMap();
            $this->dbMap->addPropertyMap(new Db\Integer('id'), 'key');
            $this->dbMap->addPropertyMap(new Db\Integer('marketId', 'market_id'));
            $this->dbMap->addPropertyMap(new Db\Integer('userId', 'user_id'));
            $this->dbMap->addPropertyMap(new Db\Integer('categoryId', 'category_id'));
            $this->dbMap->addPropertyMap(new Db\Decimal('units'));
            $this->dbMap->addPropertyMap(new Db\Text('notes'));
            $this->dbMap->addPropertyMap(new Db\Date('modified'));
            $this->dbMap->addPropertyMap(new Db\Date('created'));

        }
        return $this->dbMap;
    }

    /**
     * @return \Tk\DataMap\DataMap
     */
    public function getFormMap()
    {
        if (!$this->formMap) {
            $this->formMap = new \Tk\DataMap\DataMap();
            $this->formMap->addPropertyMap(new Form\Integer('id'), 'key');
            $this->formMap->addPropertyMap(new Form\Integer('marketId'));
            $this->formMap->addPropertyMap(new Form\Integer('userId'));
            $this->formMap->addPropertyMap(new Form\Integer('categoryId'));
            $this->formMap->addPropertyMap(new Form\Text('symbol'));
            $this->formMap->addPropertyMap(new Form\Decimal('units'));
            $this->formMap->addPropertyMap(new Form\Text('notes'));

        }
        return $this->formMap;
    }

    /**
     * @param array|Filter $filter
     * @param Tool $tool
     * @return ArrayObject|Asset[]
     * @throws \Exception
     */
    public function findFiltered($filter, $tool = null)
    {
        return $this->selectFromFilter($this->makeQuery(\Tk\Db\Filter::create($filter)), $tool);
    }

    /**
     * @param Filter $filter
     * @return Filter
     */
    public function makeQuery(Filter $filter)
    {
        $filter->appendFrom('%s a', $this->quoteParameter($this->getTable()));

        if (!empty($filter['keywords'])) {
            $kw = '%' . $this->escapeString($filter['keywords']) . '%';
            $w = '';
            $w .= sprintf('a.symbol LIKE %s OR ', $this->quote($kw));
            if (is_numeric($filter['keywords'])) {
                $id = (int)$filter['keywords'];
                $w .= sprintf('a.id = %d OR ', $id);
            }
            if ($w) $filter->appendWhere('(%s) AND ', substr($w, 0, -3));
        }

        if (isset($filter['id'])) {     // Must be able to get records with 0 'asset_id' value for total
            $w = $this->makeMultiQuery($filter['id'], 'a.id');
            if ($w) $filter->appendWhere('(%s) AND ', $w);
        }
        if (!empty($filter['marketId'])) {
            $w = $this->makeMultiQuery($filter['marketId'], 'a.market_id');
            if ($w) $filter->appendWhere('(%s) AND ', $w);
        }
        if (!empty($filter['categoryId'])) {
            $w = $this->makeMultiQuery($filter['categoryId'], 'a.category_id');
            if ($w) $filter->appendWhere('(%s) AND ', $w);
        }

        if (!empty($filter['userId'])) {
            $filter->appendWhere('a.user_id = %s AND ', (int)$filter['userId']);
        }

        if (!empty($filter['active']) && $filter['active'] !== '' && $filter['active'] !== null) {
            $filter->appendFrom(', market b');
            $filter->appendWhere('a.market_id = b.id AND b.active = %s AND ', (int)$filter['active']);
        }

        if (!empty($filter['exclude'])) {
            $w = $this->makeMultiQuery($filter['exclude'], 'a.id', 'AND', '!=');
            if ($w) $filter->appendWhere('(%s) AND ', $w);
        }

        return $filter;
    }



    /**
     * @param int $assetId
     * @param string $currency
     * @param \DateTime $dateFrom
     * @param null|\Tk\Db\Tool $tool
     * @return array
     * @throws \Tk\Db\Exception
     */
    public function findAssetTotals($assetId, $currency, $dateFrom = null, $tool = null)
    {
        if (!$tool)
            $tool = \Tk\Db\Tool::create('created DESC');
        if (!$dateFrom)
            $dateFrom = \Tk\Date::create()->sub(new \DateInterval('P1D'));
        $stm = $this->getDb()->prepare('SELECT * FROM asset_total a WHERE asset_id = ? AND currency = ? AND created > ? ' . $tool->toSql());
        $stm->execute(array(
            $assetId, $currency, $dateFrom->format(\Tk\Date::FORMAT_ISO_DATETIME)
        ));
        return $stm->fetchAll();
    }

    /**
     * @param int $assetId
     * @param string $total  current total units owned
     * @param string $currency  EG: AUD, USD, BTC, ETH, etc
     * @param string $value  The current total value in currency terms
     * @throws \Tk\Db\Exception
     */
    public function addAssetTotal($assetId, $total, $currency, $value)
    {
        $stm = $this->getDb()->prepare('INSERT INTO asset_total (asset_id, total, currency, value, created)  VALUES (?, ?, ?, ?, NOW())');
        $stm->execute(array(
            $assetId, $total, $currency, $currency
        ));
    }


}