<?php
namespace App\Db;

use Tk\Db\Tool;
use Tk\Db\Map\ArrayObject;
use Tk\DataMap\Db;
use Tk\DataMap\Form;
use Bs\Db\Mapper;

/**
 * @author Mick Mifsud
 * @created 2019-05-30
 * @link http://tropotek.com.au/
 * @license Copyright 2019 Tropotek
 */
class ExchangeMap extends Mapper
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
            $this->dbMap->addPropertyMap(new Db\Text('driver'));
            $this->dbMap->addPropertyMap(new Db\Text('username'));
            $this->dbMap->addPropertyMap(new Db\Text('apiKey', 'api_key'));
            $this->dbMap->addPropertyMap(new Db\Text('secret'));
            $this->dbMap->addPropertyMap(new Db\Text('currency'));
            $this->dbMap->addPropertyMap(new Db\Text('icon'));
            $this->dbMap->addPropertyMap(new Db\Text('description'));
            $this->dbMap->addPropertyMap(new Db\Boolean('active'));
            $this->dbMap->addPropertyMap(new Db\JsonArray('options'));
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
            $this->formMap->addPropertyMap(new Form\Integer('userId'));
            $this->formMap->addPropertyMap(new Form\Text('driver'));
            $this->formMap->addPropertyMap(new Form\Text('username'));
            $this->formMap->addPropertyMap(new Form\Text('apiKey'));
            $this->formMap->addPropertyMap(new Form\Text('secret'));
            $this->formMap->addPropertyMap(new Form\Text('currency'));
            $this->formMap->addPropertyMap(new Form\Text('icon'));
            $this->formMap->addPropertyMap(new Form\Text('description'));
            $this->formMap->addPropertyMap(new Form\Boolean('active'));
            $this->formMap->addPropertyMap(new Form\ObjectMap('options'));
            $this->formMap->addPropertyMap(new Form\Date('modified'));
            $this->formMap->addPropertyMap(new Form\Date('created'));

        }
        return $this->formMap;
    }

    /**
     * @param array $filter
     * @param Tool $tool
     * @return ArrayObject|Exchange[]
     * @throws \Exception
     */
    public function findFiltered($filter = array(), $tool = null)
    {
        $this->makeQuery($filter, $tool, $where, $from);
        $res = $this->selectFrom($from, $where, $tool);
        return $res;
    }

    /**
     * @param array $filter
     * @param Tool $tool
     * @param string $where
     * @param string $from
     * @return ArrayObject|Exchange[]
     */
    public function makeQuery($filter = array(), $tool = null, &$where = '', &$from = '')
    {
        $from .= sprintf('%s a ', $this->quoteParameter($this->getTable()));

        if (!empty($filter['keywords'])) {
            $kw = '%' . $this->escapeString($filter['keywords']) . '%';
            $w = '';
            //$w .= sprintf('a.name LIKE %s OR ', $this->quote($kw));
            if (is_numeric($filter['keywords'])) {
                $id = (int)$filter['keywords'];
                $w .= sprintf('a.id = %d OR ', $id);
            }
            if ($w) $where .= '(' . substr($w, 0, -3) . ') AND ';

        }

        if (!empty($filter['id'])) {
            $where .= sprintf('a.id = %s AND ', (int)$filter['id']);
        }
        if (!empty($filter['userId'])) {
            $where .= sprintf('a.user_id = %s AND ', (int)$filter['userId']);
        }
        if (!empty($filter['username'])) {
            $where .= sprintf('a.username = %s AND ', $this->quote($filter['username']));
        }
        if (!empty($filter['driver'])) {
            $where .= sprintf('a.driver = %s AND ', $this->quote($filter['driver']));
        }
        if (!empty($filter['curency'])) {
            $where .= sprintf('a.currency = %s AND ', $this->quote($filter['curency']));
        }
        if (!empty($filter['apiKey'])) {
            $where .= sprintf('a.api_key = %s AND ', $this->quote($filter['apiKey']));
        }
        if (!empty($filter['secret'])) {
            $where .= sprintf('a.secret = %s AND ', $this->quote($filter['secret']));
        }
        if (isset($filter['active']) && $filter['active'] !== null && $filter['active'] !== '') {
            $where .= sprintf('a.active = %s AND ', $this->quote($filter['active']));
        }

        if (!empty($filter['exclude'])) {
            $w = $this->makeMultiQuery($filter['exclude'], 'a.id', 'AND', '!=');
            if ($w) $where .= '('. $w . ') AND ';

        }

        if ($where) {
            $where = substr($where, 0, -4);
        }
        return $where;
    }


    /**
     * @param int $exchangeId
     * @param string $market
     * @param string $currency
     * @param string $amount
     * @throws \Tk\Db\Exception
     */
    public function addEquityTotal($exchangeId, $market, $currency, $amount)
    {
        $stm = $this->getDb()->prepare('INSERT INTO equity_total (exchange_id, market, currency, amount, created)  VALUES (?, ?, ?, ?, NOW())');
        $stm->execute(array(
            $exchangeId, $market, $currency, $amount
        ));

    }

    /**
     * @param int $exchangeId
     * @param string $market
     * @param string $currency
     * @param \DateTime $dateFrom
     * @param null|\Tk\Db\Tool $tool
     * @return array
     * @throws \Tk\Db\Exception
     */
    public function findEquityTotals($exchangeId, $market, $currency, $dateFrom = null, $tool = null)
    {
        if (!$tool)
            $tool = \Tk\Db\Tool::create('created DESC');
        if (!$dateFrom)
            $dateFrom = \Tk\Date::create()->sub(new \DateInterval('P1D'));
        $stm = $this->getDb()->prepare('SELECT * FROM equity_total a WHERE exchange_id = ? AND market = ? AND currency = ? AND created > ? ' . $tool->toSql());
        $stm->execute(array(
            $exchangeId, $market, $currency, $dateFrom->format(\Tk\Date::FORMAT_ISO_DATETIME)
        ));
        return $stm->fetchAll();
    }

    /**
     * Find all available equity markets in the DB
     *
     * @param int $exchangeId
     * @param null $dateFrom
     * @param null $tool
     * @return array
     * @throws \Tk\Db\Exception
     */
    public function findEquityMarkets($exchangeId, $dateFrom = null, $tool = null)
    {
        if (!$tool)
            $tool = \Tk\Db\Tool::create('market');
        if (!$dateFrom)
            $dateFrom = \Tk\Date::create()->sub(new \DateInterval('P1D'));
        $stm = $this->getDb()->prepare('SELECT DISTINCT market FROM equity_total a WHERE exchange_id = ? AND created > ? ' . $tool->toSql());
        $stm->execute(array(
            $exchangeId, $dateFrom->format(\Tk\Date::FORMAT_ISO_DATETIME)
        ));
        $o = $stm->fetchAll(\PDO::FETCH_COLUMN);
        return $o;
    }



}
