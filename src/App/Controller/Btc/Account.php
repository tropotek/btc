<?php
namespace App\Controller\Btc;

use Tk\Request;
use Dom\Template;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Account extends \Bs\Controller\AdminIface
{

    /**
     * @var \App\Db\Exchange
     */
    protected $exchange = null;

    protected $totals = array();
    protected $equity = 0;


    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->setPageTitle('Exchange Account');
    }


    /**
     * @param \Tk\Request $request
     * @param string $exchange
     * @throws \Exception
     */
    public function doDefault(\Tk\Request $request, $exchange)
    {
        $this->exchange = \App\Db\ExchangeMap::create()->findFiltered(array('driver' => $exchange, 'userId' => $this->getConfig()->getUser()->getId()))->current();
        if (!$this->exchange) {
            \Tk\Alert::addError('Exchange not found!');
            \Tk\Uri::create()->redirect();
        }

        $this->setPageTitle($this->exchange->getName() . ' Exchange Account');


        $this->totals = \App\Db\ExchangeMap::create()->findEquityTotals($this->exchange->getId(), 'AUD');
        $this->equity = 0;
        if (count($this->totals)) {
            $this->equity = $this->totals[0]->amount;
        }


    }

    /**
     * @return Template
     * @throws \Exception
     */
    public function show()
    {
        $template = parent::show();


        $template->appendCssUrl('//cdnjs.cloudflare.com/ajax/libs/dygraph/2.1.0/dygraph.min.css');
        $template->appendJsUrl('//cdnjs.cloudflare.com/ajax/libs/dygraph/2.1.0/dygraph.min.js');
        $js = <<<JS
$(document).ready(function() {
  
  var stockData = [
     [ new Date("2009/07/12"), 100 ],
     [ new Date("2009/07/19"), 150 ]
  ];
  
  var g = new Dygraph(document.getElementById("stock_div"), stockData,
   {
     labels: [ "Date", "Amount $" ]
     customBars: true
   });
  
});
JS;


        $html = sprintf('
<div class="row">
  <div class="col-md-6"><p>Total Equity: $%.4f</p></div>
  <div class="col-md-6"><p>Available: $%.4f</p></div>
</div>
', round($this->equity, 4), round($this->exchange->getAvailableCurrency(), 4));

        $template->prependHtml('panel', $html);

        foreach ($this->totals as $t) {
            $row = $template->getRepeat('row');
            $row->insertText('amount', round($t->amount, 4));
            $row->insertText('created', $t->created);
            $row->appendRepeat();
        }






        $template->setAttr('panel', 'data-panel-icon', $this->exchange->icon);
        $template->setAttr('panel', 'data-panel-title', $this->exchange->driver . ' - [ID ' . $this->exchange->getId() . ']');
        
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
<div>

  <div class="tk-panel" data-panel-icon="fa fa-btc" var="panel">
  
    <div id="stock_div"></div>
  
    <table class="table table-bordered table-striped">
      <tr>
        <th>Amount</th>
        <th>Date</th>
      </tr>
      <tr repeat="row">
        <td var="amount"></td>
        <td var="created"></td>
      </tr>
    </table>
  
  </div>
    
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }

}