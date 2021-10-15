<?php
namespace App\Controller\Exchange;


use Tk\Table\Cell;
use Dom\Template;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Edit extends \Bs\Controller\AdminEditIface
{

    /**
     * @var \App\Db\Exchange
     */
    protected $exchange = null;

    /**
     * @var null|\Tk\Table
     */
    protected $currencyTable = null;


    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->setPageTitle('Exchange Edit');
    }


    /**
     * @param \Tk\Request $request
     * @param string $targetRole
     * @throws \Exception
     */
    public function doDefault(\Tk\Request $request, $targetRole = 'user')
    {
        $this->init($request);
        $this->getForm()->execute();

        if ($this->exchange->getId()) {

            $table = $this->currencyTable = $this->getConfig()->createTable('currency');
            $this->getConfig()->createTableRenderer($table);

            $table->appendCell(Cell\Text::create('symbol'));
            $list = $this->exchange->getApi()->fetchMarkets();
            foreach ($list as $k => $v) {
                $list[$k] = ['symbol' => $v['base']];
            }
            $table->setList($list);

            $table->execute();
        }

    }

    /**
     * @param \Tk\Request $request
     * @throws \Exception
     */
    public function init($request)
    {
        $this->exchange = new \App\Db\Exchange();
        $this->exchange->setUserId($this->getAuthUser()->getId());
        if ($request->get('exchangeId')) {
            $this->exchange = \App\Db\ExchangeMap::create()->find($request->get('exchangeId'));
        }

        $this->setForm(\App\Form\Exchange::create()->setModel($this->exchange));
    }

    /**
     * @return Template
     * @throws \Exception
     */
    public function show()
    {
        $template = parent::show();
        
        // Render the form
        $template->appendTemplate('form', $this->form->show());

        if ($this->exchange->getId())
            $template->setAttr('form', 'data-panel-title', $this->exchange->getDriver() . ' - [ID ' . $this->exchange->getId() . ']');

        if ($this->currencyTable) {
            $template->appendTemplate('table', $this->currencyTable->getRenderer()->show());
            $template->setAttr('left-panel', 'class', 'col-sm-8');
            $template->setVisible('right-panel');
        }
        
        return $template;
    }


    /**
     * DomTemplate magic method
     *
     * @return Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<div class="row">
  <div class="col-sm-12" var="left-panel">
    <div class="tk-panel" data-panel-icon="fa fa-building-o" var="form"></div>
  </div>
  <div class="col-sm-4" choice="right-panel">
    <div class="tk-panel" data-panel-icon="fa fa-building-o" data-panel-title="Available Currencies" var="table"></div>
  </div>
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }

}