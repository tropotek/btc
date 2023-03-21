<?php
namespace App\Db;

use Tk\Date;
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
class AssetTickMap extends Mapper
{

    /**
     * @return \Tk\DataMap\DataMap
     */
    public function getDbMap()
    {
        if (!$this->dbMap) { 
            $this->dbMap = new \Tk\DataMap\DataMap();
            $this->dbMap->addPropertyMap(new Db\Integer('id'), 'key');
            $this->dbMap->addPropertyMap(new Db\Integer('userId', 'user_id'));
            $this->dbMap->addPropertyMap(new Db\Integer('assetId', 'asset_id'));
            $this->dbMap->addPropertyMap(new Db\Decimal('units'));
            $this->dbMap->addPropertyMap(new Db\Text('currency'));
            $this->dbMap->addPropertyMap(new Db\Decimal('bid'));
            $this->dbMap->addPropertyMap(new Db\Decimal('ask'));
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
            $this->formMap->addPropertyMap(new Form\Integer('userId'));
            $this->formMap->addPropertyMap(new Form\Integer('assetId'));
            $this->formMap->addPropertyMap(new Form\Decimal('units'));
            $this->formMap->addPropertyMap(new Form\Text('currency'));
            $this->formMap->addPropertyMap(new Form\Decimal('bid'));
            $this->formMap->addPropertyMap(new Form\Decimal('ask'));

        }
        return $this->formMap;
    }

    /**
     * @param int $assetId
     * @return bool
     * @throws \Tk\Db\Exception
     */
    public function deleteTicksByAssetId($assetId)
    {
        $stm = $this->getDb()->prepare('DELETE FROM asset_tick WHERE asset_id = ?');
        return $stm->execute(array($assetId));
    }

    /**
     * @param int $assetId
     * @param \DateTime $start
     * @param \DateTime $end
     * @param string $period          The tick unit time frame minute|hour|day|week|month|year
     * @return ArrayObject|AssetTick[]
     */
    public function getTicksByDateRange($assetId, \DateTime $start, \DateTime $end, $period = 'minute')
    {
        switch ($period) {
            case 'minute':
                $period = "CONCAT(DATE(created), ' ', HOUR(created), ':', MINUTE(created))";
                break;
            default:
            case 'hour':
                $period = "CONCAT(DATE(created), ' ', HOUR(created))";
                break;
            case 'day':
                $period = "DATE(created)";
                break;
            case 'week':
                $period = "CONCAT(YEAR(created), '-', WEEK(created))";
                break;
            case 'month':
                $period = "CONCAT(YEAR(created), '-', MONTH(created))";
                break;
            case 'year':
                $period = "YEAR(created)";
                break;
        }

        $tool = Tool::create('created DESC');
        $tool->setGroupBy($period);
        $filter = \Tk\Db\Filter::create();
        $filter->set('assetId', $assetId);
        $filter->set('between', ['start' => $start, 'end' => $end]);
        return $this->findFiltered($filter, $tool);
    }

    /**
     * @param array|Filter $filter
     * @param Tool $tool
     * @return ArrayObject|AssetTick[]
     * @throws \Exception
     */
    public function findFiltered($filter, $tool = null)
    {
        $filter = \Tk\Db\Filter::create($filter);
        $filter->appendFrom('%s a', $this->quoteParameter('v_asset_tick'));
        return $this->selectFromFilter($this->queryView($filter), $tool);
    }

    /**
     * @param array|Filter $filter
     * @param Tool $tool
     * @return ArrayObject|AssetTick[]
     * @throws \Exception
     */
    public function findFilteredTotals($filter, $tool = null)
    {
        $filter = \Tk\Db\Filter::create($filter);
        $filter->appendFrom('%s a', $this->quoteParameter('v_tick_totals'));
        return $this->selectFromFilter($this->queryView($filter), $tool);
    }

    /**
     * @param Filter $filter
     * @return Filter
     */
    public function queryView(Filter $filter)
    {
        if (!empty($filter['id'])) {
            $w = $this->makeMultiQuery($filter['id'], 'a.id');
            if ($w) $filter->appendWhere('(%s) AND ', $w);
        }

        if (!empty($filter['userId'])) {
            $w = $this->makeMultiQuery($filter['userId'], 'a.user_id');
            if ($w) $filter->appendWhere('(%s) AND ', $w);
        }

        if (isset($filter['assetId'])) {
            $filter->appendWhere('a.asset_id = %s AND ', (int)$filter['assetId']);
        }

        if (isset($filter['categoryId'])) {
            $filter->appendWhere('a.category_id = %s AND ', (int)$filter['categoryId']);
        }

        if (!empty($filter['currency'])) {
            $filter->appendWhere('a.currency = %s AND ', $this->quote($filter['currency']));
        }

        if (!empty($filter['between'])) {
            $filter->appendWhere('a.created BETWEEN %s AND %s AND ',
                $this->quote($filter['between']['start']->format(Date::FORMAT_ISO_DATETIME)),
                $this->quote($filter['between']['end']->format(Date::FORMAT_ISO_DATETIME))
            );
        }

        if (!empty($filter['inTotal']) && $filter['inTotal'] !== '' && $filter['inTotal'] !== null) {
            $filter->appendWhere('a.in_total = %s AND ', (int)$filter['inTotal']);
        }

        if (!empty($filter['exclude'])) {
            $w = $this->makeMultiQuery($filter['exclude'], 'a.id', 'AND', '!=');
            if ($w) $filter->appendWhere('(%s) AND ', $w);
        }

        return $filter;
    }

}