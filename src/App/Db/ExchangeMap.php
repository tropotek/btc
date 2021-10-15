<?php
namespace App\Db;

use Tk\Db\Filter;
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
            //$this->dbMap->addPropertyMap(new Db\Boolean('default'));
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
            //$this->formMap->addPropertyMap(new Form\Boolean('default'));
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
     * @param array|Filter $filter
     * @param Tool $tool
     * @return ArrayObject|Exchange[]
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

        if (!empty($filter['id'])) {
            $w = $this->makeMultiQuery($filter['id'], 'a.id');
            if ($w) $filter->appendWhere('(%s) AND ', $w);
        }

        if (!empty($filter['userId'])) {
            $filter->appendWhere('a.user_id = %s AND ', (int)$filter['userId']);
        }
//        if (!empty($filter['default'])) {
//            $filter->appendWhere('a.default = %s AND ', (int)$filter['default']);
//        }
        if (!empty($filter['driver'])) {
            $filter->appendWhere('a.driver = %s AND ', $this->quote($filter['driver']));
        }
        if (!empty($filter['curency'])) {
            $filter->appendWhere('a.currency = %s AND ', $this->quote($filter['curency']));
        }
        if (!empty($filter['apiKey'])) {
            $filter->appendWhere('a.api_key = %s AND ', $this->quote($filter['apiKey']));
        }
        if (isset($filter['active']) && $filter['active'] !== null && $filter['active'] !== '') {
            $filter->appendWhere('a.active = %s AND ', $this->quote($filter['active']));
        }


        if (!empty($filter['exclude'])) {
            $w = $this->makeMultiQuery($filter['exclude'], 'a.id', 'AND', '!=');
            if ($w) $filter->appendWhere('(%s) AND ', $w);
        }

        return $filter;
    }


}
