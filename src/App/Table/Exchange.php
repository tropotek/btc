<?php
namespace App\Table;

use Tk\Form\Field;
use Tk\Table\Cell;

/**
 * Example:
 * <code>
 *   $table = new Exchange::create();
 *   $table->init();
 *   $list = ObjectMap::getObjectListing();
 *   $table->setList($list);
 *   $tableTemplate = $table->show();
 *   $template->appendTemplate($tableTemplate);
 * </code>
 * 
 * @author Mick Mifsud
 * @created 2019-05-30
 * @link http://tropotek.com.au/
 * @license Copyright 2019 Tropotek
 */
class Exchange extends \Bs\TableIface
{
    
    /**
     * @return $this
     * @throws \Exception
     */
    public function init()
    {
    
        $this->appendCell(new Cell\Checkbox('id'));
        $this->appendCell(new Cell\Text('driver'))->setLabel('Exchange')->addCss('key')
            ->setUrl(\Bs\Uri::createHomeUrl('/exchangeEdit.html'));
        $this->appendCell(new Cell\Text('username'));
        $this->appendCell(new Cell\Boolean('active'));
        //$this->appendCell(new Cell\Date('modified'));
        $this->appendCell(new Cell\Date('created'));

        // Filters
        //$this->appendFilter(new Field\Input('keywords'))->setAttr('placeholder', 'Search');

        // Actions
        //$this->appendAction(\Tk\Table\Action\Link::create('New Exchange', 'fa fa-plus', \Bs\Uri::createHomeUrl('/exchangeEdit.html')));
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
     * @return \Tk\Db\Map\ArrayObject|\App\Db\Exchange[]
     * @throws \Exception
     */
    public function findList($filter = array(), $tool = null)
    {
        if (!$tool) $tool = $this->getTool();
        try {
            $filter = array_merge($this->getFilterValues(), $filter);
            $list = \App\Db\ExchangeMap::create()->findFiltered($filter, $tool);
        } catch (\Tk\Db\Exception $e) {
            if (strstr($e->getMessage(), 'order clause') !== false) {
                $this->resetSessionTool();
                $this->getBackUrl()->redirect();
            } else throw $e;
        }
        return $list;
    }

}