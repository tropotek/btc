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


        $dateFrom = \Tk\Date::create()->sub(new \DateInterval('P2D'));
        $this->totals = \App\Db\ExchangeMap::create()->findEquityTotals($this->exchange->getId(), $this->exchange->getCurrency(), $dateFrom, \Tk\Db\Tool::create('created '));
        $this->equity = 0;
        if (count($this->totals)) {
            $this->equity = $this->totals[count($this->totals)-1]->amount;
        }

        if ($request->has('get')) {
             $this->doData($request);
        }


    }

    public function doData(\Tk\Request $request)
    {
        foreach ($this->totals as $t) {
            $data[] = [$t->created, $t->amount];
        }

        \Tk\ResponseJson::createJson($data)->send();
        exit;
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
$(document).ready(function () {

  function getData(onSuccess) {
    $.get(document.location, {'get': 't'}, function (data) {
      var d = [];
      $.each(data, function (i, v) {
        v[0] = new Date(v[0]);
        v[1] = parseFloat(v[1]);
        d.push(v);
      });
      onSuccess.apply(this, [d]);
    });
  }
    
  var g = null;
  getData(function (data) {
    g = new Dygraph(document.getElementById("stock_div"), data,
      {
        labels: ["Date", "Amount $"],
        title: 'Equity vs Time',
        //showRangeSelector: true,
        //legend: 'always',
        // customBars: true,
      });
    $('#stock_div .dygraph-legend').css('top', '-15px');
    window.intervalId = setInterval(function () {
      getData(function (data) {
        console.log(data);
        g.updateOptions({'file': data});
      });
    }, 5 * 60000);
  });


});
JS;
        $template->appendJs($js);


        $html = sprintf('
<div class="row" style="background-color: #EFEFEF;padding-top: 10px;">
  <div class="col-md-6"><p>Total Equity: $%.4f</p></div>
  <div class="col-md-6"><p>Available: $%.4f</p></div>
</div>
', round($this->equity, 4), round($this->exchange->getAvailableCurrency(), 4));

        $template->prependHtml('panel', $html);

        $template->setAttr('panel', 'data-panel-icon', $this->exchange->icon);
        $template->setAttr('panel', 'data-panel-title', $this->exchange->driver . ' - [ID ' . $this->exchange->getId() . ']');


//        foreach ($this->totals as $t) {
//            $row = $template->getRepeat('row');
//            $row->insertText('amount', round($t->amount, 4));
//            $row->insertText('created', $t->created);
//            $row->appendRepeat();
//            $tyemplate->show('table');
//        }
        
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
    
    <p>&nbsp;</p>
    <div class="row" id="stock_div" style="width: 100%; height: 500px;"></div>
    <p>&nbsp;</p>
    
    <table class="table table-bordered table-striped" choice="table">
      <tr>
        <th>Equity</th>
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