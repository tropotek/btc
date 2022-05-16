<?php
namespace App\Controller\Member;

use App\Db\Asset;
use App\Db\AssetTickMap;
use Bs\Controller\ManagerTrait;
use Tk\Date;
use Tk\Db\Tool;
use Tk\Request;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Dashboard extends \Bs\Controller\AdminIface
{
    use ManagerTrait;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->setPageTitle('My Account');
        $this->getActionPanel()->setEnabled(false);
        $this->getCrumbs()->reset();
    }

    /**
     * @param Request $request
     */
    public function doDefault(Request $request)
    {

        $this->setTable(\App\Table\Asset::create());
        $this->getTable()->setEditUrl(\Bs\Uri::createHomeUrl('/assetEdit.html'));
        $this->getTable()->init();
        $this->getTable()->removeCell('id');
        $this->getTable()->removeCell('created');
        $this->getTable()->removeAction('delete');

        $filter = array('userId' => $this->getAuthUser()->getId());
        $this->getTable()->setList($this->getTable()->findList($filter, $this->getTable()->getTool('', 50)));

    }

    /**
     * @param string $type
     * @return float|int|mixed
     * @throws \Exception
     */
    public function calculateTotal($type = 'bid')
    {
        $total = 0;
        $tick = AssetTickMap::create()->findFiltered(['userId' => $this->getAuthUser()->getId(), 'assetId' => 0], Tool::create('created DESC'))->current();
        if ($tick) {
            if ($type == Asset::MARKET_BID)
                $total = $tick->getBid();
            else
                $total = $tick->getAsk();
        }
        return $total;
    }

    public function show()
    {
        $template = parent::show();

        $template->insertText('total', '$' . number_format($this->calculateTotal(), 2));
        $list = Asset::getUserTotalsHistory($this->getAuthUser()->getId(), 160);
        $list = array_reverse($list);
        $str = implode(',', $list);
        $template->insertText('totalGraph', $str);

        $template->appendTemplate('panel', $this->getTable()->show());

        $js = <<<JS
jQuery(function($) {
  $('.tk-graph2').each(function () {
    $(this).sparkline('html', {
      height: '55px',
      
      lineWidth: 3,
      lineColor: 'white',
      spotRadius: 0,
      fillColor: 'transparent',
      enableTagOptions: true
    });
  });
})
JS;
        $template->appendJs($js);

        $css = <<<CSS
.table tbody > tr > td {
  font-size: 1rem;
}
.table tbody > tr > td a {
  font-size: 1rem;
}
.table thead th {
  text-align: center !important;
}
.table thead th.key {
  text-align: left !important;
}

.infographic-box .emerald-bg .fa {

}
.infographic-box .graph-wrap {
  
  /* 0399e2 */
  border: 1px solid #c0e8ff;
  border-width: 0 0 1px 1px;
  background-color: transparent;
  float: right;
  padding: 3px;
  margin: 0 0 0 0px;
  width: 60%;
  height: 65px;
  overflow: hidden;
  
}
.infographic-box .tk-graph2 {
  background-color: transparent;
  /*background-color: rgba(255, 255, 255, 0.5);*/
  text-align: right;
}

.infographic-box .tk-graph2 canvas {

  /*min-width: 100%;*/
}
CSS;
        $template->appendCss($css);

        return $template;
    }


    /**
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<div class="">
  
  <div class="row">
    <div class="col-sm-6">
      <div class="main-box infographic-box colored green-bg">
        <i class="fa fa-money"></i>
        <span class="headline">Total</span>
        <span class="value" var="total" ></span>
      </div>
    </div>
    <div class="col-sm-6">
      <div class="main-box infographic-box colored emerald-bg clearfix">
        <i class="fa fa-line-chart"></i>
        <div class="graph-wrap">
          <span class="tk-graph2 float-right" var="totalGraph"></span>
        </div>
      </div>
    </div>   
  </div>
  
  <div class="tk-panel" data-panel-icon="fa fa-bar-chart" var="panel"></div>
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }

}