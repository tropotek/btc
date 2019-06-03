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

    protected $days = 2;
    protected $dateFrom = null;


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


        $this->days = (int)$request->get('d', 2);
        $this->dateFrom = \Tk\Date::create()->sub(new \DateInterval('P'.$this->days.'D'));

        $this->setPageTitle($this->exchange->getName() . ' Exchange Account');


        if ($request->has('get')) {
             $this->doData($request);
        }


    }

    /**
     * @param string $market
     * @throws \Tk\Db\Exception
     */
    protected function getMarketData($market = 'ALL')
    {
        return \App\Db\ExchangeMap::create()->findEquityTotals($this->exchange->getId(), $market,
            $this->exchange->getCurrency(), $this->dateFrom, \Tk\Db\Tool::create('created'));
    }

    
    /**
     * @param Request $request
     * @throws \Tk\Db\Exception
     */
    public function doData(\Tk\Request $request)
    {
        $totals = $this->getMarketData($request->get('m'));
        $data = [];
        foreach ($totals as $t) {
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

        $this->getActionPanel()->append(\Tk\Ui\Link::createBtn('Account Summary',
            \Bs\Uri::createHomeUrl('/'.$this->exchange->driver.'/summary.html'), 'fa fa-list-alt'));

        // TODO: Ajax this....
        $this->getActionPanel()->append(\Tk\Ui\Link::createBtn('1 Day', \Tk\Uri::create()->set('d', '1')));
        $this->getActionPanel()->append(\Tk\Ui\Link::createBtn('2 Days', \Tk\Uri::create()->set('d', '2')));
        $this->getActionPanel()->append(\Tk\Ui\Link::createBtn('3 Days', \Tk\Uri::create()->set('d', '3')));
        $this->getActionPanel()->append(\Tk\Ui\Link::createBtn('5 Days', \Tk\Uri::create()->set('d', '5')));
        $this->getActionPanel()->append(\Tk\Ui\Link::createBtn('7 Days', \Tk\Uri::create()->set('d', '7')));
        $this->getActionPanel()->append(\Tk\Ui\Link::createBtn('14 Days', \Tk\Uri::create()->set('d', '14')));
        $this->getActionPanel()->append(\Tk\Ui\Link::createBtn('28 Days', \Tk\Uri::create()->set('d', '28')));
        $this->getActionPanel()->append(\Tk\Ui\Link::createBtn('1 Year', \Tk\Uri::create()->set('d', '365')));

        $template = parent::show();

        $html = sprintf('
<div class="row" style="background-color: #EFEFEF;padding-top: 10px;">
  <div class="col-md-6"><p>Total Equity: $%.4f</p></div>
  <div class="col-md-6"><p>Available: $%.4f</p></div>
</div>
', round($this->exchange->getTotalEquity(), 4), round($this->exchange->getAvailableCurrency(), 4));

        $template->prependHtml('panel', $html);
        $template->setAttr('panel', 'data-panel-icon', $this->exchange->getIcon());
        $template->setAttr('panel', 'data-panel-title',
            $this->exchange->getDriver() . ' [ID ' . $this->exchange->getId() . '] - ' .
            $this->days . ' Days - ' . '[' . $this->exchange->getCurrency() . ']'
        );


        $template->appendCssUrl('//cdnjs.cloudflare.com/ajax/libs/dygraph/2.1.0/dygraph.min.css');
        $template->appendJsUrl('//cdnjs.cloudflare.com/ajax/libs/dygraph/2.1.0/dygraph.min.js');

        $js = <<<JS
$(document).ready(function () {

  function getData(market, onSuccess) {
    $.get(document.location, {'get': 't', 'm': market}, function (data) {
      var d = [];
      $.each(data, function (i, v) {
        v[0] = new Date(v[0]);
        v[1] = parseFloat(v[1]);
        d.push(v);
      });
      onSuccess.apply(this, [d, market]);
    });
  }
  
  $('div.graph').each(function () {
    var div = $(this);
    var g = null;
    getData(div.data('market'), function (data) {
      g = new Dygraph(div.get(0), data,
        {
          labels: ["Date", "Amount $"],
          title: div.data('name') + ' [' + div.data('market') + '] - Vol: ' + div.data('vol'),
          //showRangeSelector: true,
          //legend: 'always',
          // customBars: true,
        });
      div.find('.dygraph-legend').css('top', '-15px');
      window.intervalId = setInterval(function () {
        getData(div.data('market') ,function (data) {
          g.updateOptions({'file': data});
        });
      }, 5 * 60 * 1000);
    });
  
  });

});
JS;
        $template->appendJs($js);

        $balance = $this->exchange->getApi()->fetchBalance();
        $volumeList = $balance['total'];

        $marketList = \App\Db\ExchangeMap::create()->findEquityMarkets($this->exchange->getId(), $this->dateFrom);
        foreach ($marketList as $market) {
            $row = $template->getRepeat('graph');
            $row->setAttr('graph', 'data-market', $market);
            $row->setAttr('graph', 'data-name', $this->exchange->getMarketName($market));
            $row->setAttr('graph', 'data-days', $this->days);
            $vol = 0;
            if (!empty($volumeList[$market]))
                $vol = $volumeList[$market];
            $row->setAttr('graph', 'data-vol', \ccxt\Exchange::number_to_string($vol));
            $row->appendRepeat();
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
<div>

  <div class="tk-panel" data-panel-icon="fa fa-btc" var="panel">
    
    
    <div class="row">
      <div class="graph col-md-6" style="width: 100%; height: 300px;" var="graph" repeat="graph"></div>
    </div>
    
    
  </div>
    
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }

}