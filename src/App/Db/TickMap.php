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
 * @created 2020-11-22
 * @link http://tropotek.com.au/
 * @license Copyright 2020 Tropotek
 */
class TickMap extends Mapper
{

    /**
     * @param \Tk\Db\Pdo|null $db
     * @throws \Exception
     */
    public function __construct($db = null)
    {
        $this->dispatcher = $this->getConfig()->getEventDispatcher();
        parent::__construct($db);
        $this->setMarkDeleted();
    }

    /**
     * @return \Tk\DataMap\DataMap
     */
    public function getDbMap()
    {
        if (!$this->dbMap) { 
            $this->dbMap = new \Tk\DataMap\DataMap();
            //$this->dbMap->setEnableDynamicParameters(false);
            $this->dbMap->addPropertyMap(new Db\Integer('id'), 'key');
            $this->dbMap->addPropertyMap(new Db\Integer('exchangeId', 'exchange_id'));
            $this->dbMap->addPropertyMap(new Db\Text('symbol'));
            $this->dbMap->addPropertyMap(new Db\Text('timestamp'));
            $this->dbMap->addPropertyMap(new Db\Date('datetime'));
            $this->dbMap->addPropertyMap(new Db\Decimal('high'));
            $this->dbMap->addPropertyMap(new Db\Decimal('bid'));
            $this->dbMap->addPropertyMap(new Db\Decimal('ask'));
            $this->dbMap->addPropertyMap(new Db\Decimal('last'));
            $this->dbMap->addPropertyMap(new Db\Decimal('baseVolume', 'base_volume'));
            $this->dbMap->addPropertyMap(new Db\Decimal('quoteVolume', 'quote_volume'));
            $this->dbMap->addPropertyMap(new Db\Decimal('change'));
            $this->dbMap->addPropertyMap(new Db\Decimal('percentage'));
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
            //$this->formMap->setEnableDynamicParameters(false);
            //$this->formMap->addPropertyMap(new Form\Integer('id'), 'key');
            $this->formMap->addPropertyMap(new Form\Integer('exchangeId'));
            $this->formMap->addPropertyMap(new Form\Text('symbol'));
            $this->formMap->addPropertyMap(new Form\Text('timestamp'));
            $this->formMap->addPropertyMap(new Form\Date('datetime'));
            $this->formMap->addPropertyMap(new Form\Decimal('high'));
            $this->formMap->addPropertyMap(new Form\Decimal('bid'));
            $this->formMap->addPropertyMap(new Form\Decimal('ask'));
            $this->formMap->addPropertyMap(new Form\Decimal('last'));
            $this->formMap->addPropertyMap(new Form\Decimal('baseVolume'));
            $this->formMap->addPropertyMap(new Form\Decimal('quoteVolume'));
            $this->formMap->addPropertyMap(new Form\Decimal('change'));
            $this->formMap->addPropertyMap(new Form\Decimal('percentage'));
        }
        return $this->formMap;
    }

    /**
     * @param array|Filter $filter
     * @param Tool $tool
     * @return ArrayObject|Tick[]
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
            //$w .= sprintf('a.name LIKE %s OR ', $this->quote($kw));
            if (is_numeric($filter['keywords'])) {
                $id = (int)$filter['keywords'];
                $w .= sprintf('a.id = %d OR ', $id);
            }
            if ($w) $filter->appendWhere('(%s) AND ', substr($w, 0, -3));
        }

        if (isset($filter['id'])) {
            $w = $this->makeMultiQuery($filter['id'], 'a.id');
            if ($w) $filter->appendWhere('(%s) AND ', $w);
        }
        if (isset($filter['exchangeId'])) {
            $filter->appendWhere('a.exchange_id = %s AND ', (int)$filter['exchangeId']);
        }
        if (!empty($filter['symbol'])) {
            $filter->appendWhere('a.symbol = %s AND ', $this->quote($filter['symbol']));
        }
        if (!empty($filter['timestamp'])) {
            $filter->appendWhere('a.timestamp = %s AND ', (int)$filter['timestamp']);
        }
        if (!empty($filter['datetime'])) {
            $filter->appendWhere('a.datetime = %s AND ', $filter['datetime']->format(\Tk\Date::FORMAT_ISO_DATETIME));
        }
        if (!empty($filter['high'])) {
            $filter->appendWhere('a.high = %s AND ', (float)$filter['high']);
        }
        if (!empty($filter['bid'])) {
            $filter->appendWhere('a.bid = %s AND ', (float)$filter['bid']);
        }
        if (!empty($filter['ask'])) {
            $filter->appendWhere('a.ask = %s AND ', (float)$filter['ask']);
        }
        if (!empty($filter['last'])) {
            $filter->appendWhere('a.last = %s AND ', (float)$filter['last']);
        }
        if (!empty($filter['change'])) {
            $filter->appendWhere('a.change = %s AND ', (float)$filter['change']);
        }
        if (!empty($filter['percent'])) {
            $filter->appendWhere('a.percent = %s AND ', (float)$filter['percent']);
        }

        if (!empty($filter['exclude'])) {
            $w = $this->makeMultiQuery($filter['exclude'], 'a.id', 'AND', '!=');
            if ($w) $filter->appendWhere('(%s) AND ', $w);
        }

        return $filter;
    }

}