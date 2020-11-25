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
 * @created 2020-11-25
 * @link http://tropotek.com.au/
 * @license Copyright 2020 Tropotek
 */
class CandleMap extends Mapper
{
    /**
     * @param \Tk\Db\Pdo|null $db
     * @throws \Exception
     */
    public function __construct($db = null)
    {
        $this->dispatcher = $this->getConfig()->getEventDispatcher();
        parent::__construct($db);
        $this->setMarkDeleted('');
    }

    /**
     * @return \Tk\DataMap\DataMap
     */
    public function getDbMap()
    {
        if (!$this->dbMap) { 
            $this->dbMap = new \Tk\DataMap\DataMap();
            $this->dbMap->addPropertyMap(new Db\Integer('id'), 'key');
            $this->dbMap->addPropertyMap(new Db\Integer('exchangeId', 'exchange_id'));
            $this->dbMap->addPropertyMap(new Db\Text('symbol'));
            $this->dbMap->addPropertyMap(new Db\Text('period'));
            $this->dbMap->addPropertyMap(new Db\Integer('timestamp'));
            $this->dbMap->addPropertyMap(new Db\Decimal('open'));
            $this->dbMap->addPropertyMap(new Db\Decimal('high'));
            $this->dbMap->addPropertyMap(new Db\Decimal('low'));
            $this->dbMap->addPropertyMap(new Db\Decimal('close'));
            $this->dbMap->addPropertyMap(new Db\Decimal('volume'));

        }
        return $this->dbMap;
    }

    /**
     * @return \Tk\DataMap\DataMap
     */
//    public function getFormMap()
//    {
//        if (!$this->formMap) {
//            $this->formMap = new \Tk\DataMap\DataMap();
//            $this->formMap->addPropertyMap(new Form\Integer('id'), 'key');
//            $this->formMap->addPropertyMap(new Form\Integer('exchangeId'));
//            $this->formMap->addPropertyMap(new Form\Text('symbol'));
//            $this->formMap->addPropertyMap(new Form\Text('period'));
//            $this->formMap->addPropertyMap(new Form\Integer('timestamp'));
//            $this->formMap->addPropertyMap(new Form\Decimal('open'));
//            $this->formMap->addPropertyMap(new Form\Decimal('high'));
//            $this->formMap->addPropertyMap(new Form\Decimal('low'));
//            $this->formMap->addPropertyMap(new Form\Decimal('close'));
//            $this->formMap->addPropertyMap(new Form\Decimal('volume'));
//
//        }
//        return $this->formMap;
//    }

    /**
     * @param array|Filter $filter
     * @param Tool $tool
     * @return ArrayObject|Candle[]
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
        if (!empty($filter['period'])) {
            $filter->appendWhere('a.period = %s AND ', $this->quote($filter['period']));
        }
        if (isset($filter['timestamp'])) {
            $filter->appendWhere('a.timestamp = %s AND ', (int)$filter['timestamp']);
        }
        if (!empty($filter['open'])) {
            $filter->appendWhere('a.open = %s AND ', (float)$filter['open']);
        }
        if (!empty($filter['high'])) {
            $filter->appendWhere('a.high = %s AND ', (float)$filter['high']);
        }
        if (!empty($filter['low'])) {
            $filter->appendWhere('a.low = %s AND ', (float)$filter['low']);
        }
        if (!empty($filter['close'])) {
            $filter->appendWhere('a.close = %s AND ', (float)$filter['close']);
        }
        if (!empty($filter['volume'])) {
            $filter->appendWhere('a.volume = %s AND ', (float)$filter['volume']);
        }

        if (!empty($filter['exclude'])) {
            $w = $this->makeMultiQuery($filter['exclude'], 'a.id', 'AND', '!=');
            if ($w) $filter->appendWhere('(%s) AND ', $w);
        }

        return $filter;
    }

}