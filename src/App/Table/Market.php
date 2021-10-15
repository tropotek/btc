<?php
namespace App\Table;

use Tk\Form\Field;
use Tk\Table\Cell;

/**
 * Example:
 * <code>
 *   $table = new Market::create();
 *   $table->init();
 *   $list = ObjectMap::getObjectListing();
 *   $table->setList($list);
 *   $tableTemplate = $table->show();
 *   $template->appendTemplate($tableTemplate);
 * </code>
 *
 * @author Mick Mifsud
 * @created 2021-10-15
 * @link http://tropotek.com.au/
 * @license Copyright 2021 Tropotek
 */
class Market extends \Bs\TableIface
{

    /**
     * @return $this
     * @throws \Exception
     */
    public function init()
    {

        $this->appendCell(new Cell\Checkbox('id'));
        $this->appendCell(new Cell\Text('symbol'));
        $this->appendCell(new Cell\Text('name'))->addCss('key')->setUrl($this->getEditUrl());
        //$this->appendCell(new Cell\Text('image'));
        //$this->appendCell(new Cell\Date('modified'));
        $this->appendCell(new Cell\Text('exchangeId'))->addOnPropertyValue(function (\Tk\Table\Cell\Iface $cell, \App\Db\Market $obj, $value) {
            $value = 'N/A';
            if ($obj->getExchange()) {
                $value = $obj->getExchange()->getName();
            }
            return $value;
        });
        $this->appendCell(new Cell\Date('created'));

        // Filters
        $this->appendFilter(new Field\Input('keywords'))->setAttr('placeholder', 'Search');

        // Actions
        //$this->appendAction(\Tk\Table\Action\Link::createLink('New Market', \Bs\Uri::createHomeUrl('/marketEdit.html'), 'fa fa-plus'));
        //$this->appendAction(\Tk\Table\Action\ColumnSelect::create()->setUnselected(array('modified', 'created')));
        $this->appendAction(\Tk\Table\Action\Delete::create());
        $this->appendAction(\Tk\Table\Action\Csv::create());

        // load table
        //$this->setList($this->findList());

        return $this;
    }

    /**
     * @param array $filter
     * @param null|\Tk\Db\Tool $tool
     * @return \Tk\Db\Map\ArrayObject|\App\Db\Market[]
     * @throws \Exception
     */
    public function findList($filter = array(), $tool = null)
    {
        if (!$tool) $tool = $this->getTool();
        try {
            $filter = array_merge($this->getFilterValues(), $filter);
            $list = \App\Db\MarketMap::create()->findFiltered($filter, $tool);
        } catch (\Tk\Db\Exception $e) {
            if (strstr($e->getMessage(), 'order clause') !== false) {
                $this->resetSessionTool();
                $this->getBackUrl()->redirect();
            } else throw $e;
        }
        return $list;
    }

}