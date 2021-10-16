<?php
namespace App\Controller\Member;

use App\Db\Asset;
use App\Db\AssetTick;
use App\Db\AssetTickMap;
use Bs\Controller\ManagerTrait;
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
        if ($type == Asset::MARKET_BID)
            $total = $tick->getBid();
        else
            $total = $tick->getAsk();

        /*
         * // TODO: this would calculate it closer to the last tick but may not be required
        $list = \App\Db\AssetMap::create()->findFiltered(['userId' => $this->getAuthUser()->getId()]);
        foreach ($list as $asset) {
            if (!$asset->getMarket() && !$asset->getMarket()->getExchange() && $asset->getMarket()->getExchange() != $currency)
                continue;
            $t = round($asset->getMarketTotalValue(), 2);
            $total = $total + $t;
        }
        */
        return $total;
    }

    public function show()
    {
        $template = parent::show();

        $template->insertText('total', '$' . number_format($this->calculateTotal(), 2));
        $list = Asset::getUserTotalsHistory($this->getAuthUser()->getId(), 65);
        $list = array_reverse($list);
        $str = implode(',', $list);
        $template->insertText('totalGraph', $str);

        $template->appendTemplate('panel', $this->getTable()->show());

        $css = <<<CSS
.table tbody > tr > td {
  font-size: 1rem;
}
.table tbody > tr > td a {
  font-size: 1rem;
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
  
  <div class="tk-panel tk-panel-totals" data-panel-icon="" data-panel-title="">
    <div class="row">
      <div class="col-md-6 text-right" style="line-height: 50px;"><strong>Total:</strong> <span var="total">$0.00</span></div>
      <div class="col-md-6 text-left"><span class="tk-graph" style="text-align: right;background: #EFEFEF;display: inline-block; padding: 3px;margin: 5px 25px;min-width: 200px;" var="totalGraph"></span></div>
    </div>
  </div>

  <div class="tk-panel" data-panel-icon="fa fa-bar-chart" var="panel" repeat1="panel">
     
  </div>
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }

}