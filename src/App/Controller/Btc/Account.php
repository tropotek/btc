<?php
namespace App\Controller\Btc;

use Tk\Db\Tool;
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
        $this->exchange = \App\Db\ExchangeMap::create()->findFiltered(array('driver' => $exchange, 'userId' => $this->getConfig()->getAuthUser()->getId()))->current();
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
            $amt = $t->amount;
            $data[] = [$t->created, $amt];
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
//
//        $this->getActionPanel()->append(\Tk\Ui\Link::createBtn('Account Summary',
//            \Bs\Uri::createHomeUrl('/index.html'), 'fa fa-list-alt'));

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

        $equity = round($this->exchange->getTotalEquity(), 2);
        $available = round($this->exchange->getAvailableCurrency(), 2);
        $total = round($equity+$available, 2);

        $html = sprintf('
<div class="row" style="background-color: #EFEFEF;padding-top: 10px;">
  <div class="col-md-4 text-center"><p>Equity<br/><b>$%.2f</b></p></div>
  <div class="col-md-4 text-center"><p>Total<br/><b>$%.2f</b></p></div>
  <div class="col-md-4 text-center"><p>Available<br/><b>$%.2f</b></p></div>
</div>
', $equity, $total, $available);

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
    $.get(document.location, {'get': 't', 'm': market, 'nolog': '1'}, function (data) {
    //$.get(document.location, {'get': 't', 'm': market}, function (data) {
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
          ylabel: div.data('currency'),
          labels: ["Date", div.data('currency')],
          title: '[' + div.data('market') + '] ' + div.data('name') + div.data('vol')
        });
      div.find('.dygraph-legend').css('top', '-15px');
      div.find('.dygraph-label.dygraph-title').css('font-size', '15px');
      window.intervalId = setInterval(function () {
        getData(div.data('market') ,function (data) {
          g.updateOptions({'file': data});
        });
      }, 3 * 60 * 1000);
    });
  
  });

});
JS;
        $template->appendJs($js);

        $css = <<<CSS
.dygraph-label .tk-dn {
  color: red;
}
.dygraph-label .tk-up {
  color: green;
}
CSS;
        $template->appendCss($css);

        $marketList = \App\Db\ExchangeMap::create()
            ->findEquityMarkets($this->exchange->getId(), $this->dateFrom, Tool::create('FIELD(market, \'BTC\', \'ALL\', \'TOTAL\') DESC, market'));
        foreach ($marketList as $market) {
            $row = $template->getRepeat('graph');
            $row->setAttr('graph', 'data-market', $market);
            $row->setAttr('graph', 'data-currency', $this->exchange->getCurrency());
            $row->setAttr('graph', 'data-name', $this->exchange->getMarketName($market));
            $row->setAttr('graph', 'data-days', $this->days);
            $vol = '';
            try {
                $marketId = $market . '/' . $this->exchange->getCurrency();
                $this->exchange->getApi()->loadMarkets();
                $tick = $this->exchange->getApi()->fetchTicker($marketId);
                $color = '';
                if ($tick['percentage'] < 0) $color = 'tk-dn';
                if ($tick['percentage'] > 0) $color = 'tk-up';
                $vol .= sprintf(' : <span class="value" title="Value %s">$%01.2f</span>', $this->exchange->getCurrency(), $tick['ask']);
                $vol .= sprintf(' [<span class="change %s" title="24h Change %%">%s%%</span>]', $color, $tick['percentage']);
            } catch (\Exception $e) { }

            $row->setAttr('graph', 'data-vol', $vol);
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
      <div class="graph col-md-6" style="width: 100%; height: 300px; margin-top: 25px;" var="graph" repeat="graph"></div>
    </div>
  </div>
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }

}