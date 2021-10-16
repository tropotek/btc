<?php
namespace App\Controller\Asset;

use Bs\Controller\AdminEditIface;
use Dom\Template;
use Tk\Date;
use Tk\Request;

/**
 * TODO: Add Route to routes.php:
 *      $routes->add('asset-edit', Route::create('/staff/assetEdit.html', 'App\Controller\Asset\Edit::doDefault'));
 *
 * @author Mick Mifsud
 * @created 2021-10-15
 * @link http://tropotek.com.au/
 * @license Copyright 2021 Tropotek
 */
class Edit extends AdminEditIface
{

    /**
     * @var \App\Db\Asset
     */
    protected $asset = null;
    protected $days = 288;


    /**
     * Iface constructor.
     */
    public function __construct()
    {
        $this->setPageTitle('Asset Edit');
    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function doDefault(Request $request)
    {

        $this->asset = new \App\Db\Asset();
        if ($request->get('assetId')) {
            $this->asset = \App\Db\AssetMap::create()->find($request->get('assetId'));
        }
        if ($request->has('get')) {
            $this->doData($request);
        }

        $this->setForm(\App\Form\Asset::create()->setModel($this->asset));
        $this->initForm($request);
        $this->getForm()->execute();
    }

    /**
     * @param Request $request
     * @throws \Tk\Db\Exception
     */
    public function doData(\Tk\Request $request)
    {
        //$totals = $this->getMarketData($request->get('m'));
        $totals = $this->asset->getAssetTotalHistory($this->days);
        $data = [];
        foreach ($totals as $c => $t) {
            $date = Date::create($c);
            $data[] = [$date->format(Date::FORMAT_ISO_DATETIME), $t];
        }
        $data = array_reverse($data);
        \Tk\ResponseJson::createJson($data)->send();
        exit;
    }

    /**
     * Add actions here
     */
    public function initActionPanel()
    {
        $this->getActionPanel()->append(\Tk\Ui\Link::createBtn('Add Market',
            \Bs\Uri::createHomeUrl('/marketEdit.html'), 'fa fa-money fa-add-action'));
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $this->initActionPanel();
        $template = parent::show();

        // Render the form
        $template->appendTemplate('panel', $this->getForm()->show());

        if ($this->asset->getId()) {
            $template->setAttr('left-panel', 'class', 'col-sm-8');
            $template->setVisible('right-panel');

            // historic profit graph
            $template->setAttr('graph', 'data-market', $this->asset->getMarket()->getName());
            $template->setAttr('graph', 'data-asset-id', $this->asset->getId());
            $template->setAttr('graph', 'data-currency', $this->asset->getMarket()->getExchange()->getCurrency());
            $template->setAttr('graph', 'data-name', $this->asset->getMarket()->getExchange()->getName());
            $template->setAttr('graph', 'data-symbol', $this->asset->getMarket()->getSymbol());
            $template->setAttr('graph', 'data-days', $this->days);


            $template->appendCssUrl('//cdnjs.cloudflare.com/ajax/libs/dygraph/2.1.0/dygraph.min.css');
            $template->appendJsUrl('//cdnjs.cloudflare.com/ajax/libs/dygraph/2.1.0/dygraph.min.js');


        $js = <<<JS
$(document).ready(function () {

  function getData(onSuccess) {
    $.get(document.location, {'get': 't', 'nolog1': '1'}, function (data) {
      var d = [];
      console.log(data);
      $.each(data, function (i, v) {
        v[0] = new Date(v[0]);
        v[1] = parseFloat(v[1]);
        d.push(v);
      });
      onSuccess.apply(this, [d]);
    });
  }
  
  $('div.graph').each(function () {
    var div = $(this);
    var g = null;
    getData(function (data) {
      g = new Dygraph(div.get(0), data,
        {
          ylabel: div.data('currency'),
          labels: ["Date", div.data('currency')],
          title: '[' + div.data('market') + ' - ' +div.data('symbol')+'] ' + div.data('name') + ' - ' + div.data('currency')
        });
      div.find('.dygraph-legend').css('bottom', '0px').css('top', '30px').css('bottom', 'unset').css('left', 'unset').css('left', '25%');
      // div.find('.dygraph-legend').css('background', 'transparent').css('bottom', '0px').css('right', '50%');
      div.find('.dygraph-label.dygraph-title').css('font-size', '15px');
      window.intervalId = setInterval(function () {
        getData(function (data) {
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

        }



        return $template;
    }

    /**
     * @return Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<div class="row">
  <div class="col-sm-12" var="left-panel">
    <div class="tk-panel" data-panel-title="Asset Edit" data-panel-icon="fa fa-btc" var="panel"></div>
  </div>
  <div class="col-sm-4" choice="right-panel">
    <div class="tk-panel" data-panel-icon="fa fa-btc" data-panel-title="Last 24h Asset Value" var="right">
      <div class="graph" style="max-width: 100%;  margin-top: 25px;" var="graph"></div>
    </div>
  </div>
</div>

HTML;
        return \Dom\Loader::load($xhtml);
    }

}