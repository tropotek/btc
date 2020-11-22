<?php
namespace App\Controller\Member;

use App\Db\Exchange;
use Tk\Request;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Dashboard extends \Bs\Controller\AdminIface
{

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

    }

    public function show()
    {
        $template = parent::show();
        $css = <<<CSS
.tr-up {
  color: green;
}
.tr-dn {
  color: red;
}
CSS;
        $template->appendCss($css);


        $exchangeList = \App\Db\ExchangeMap::create()->findFiltered(array('userId' => $this->getConfig()->getAuthUser()->getId()), \Tk\Db\Tool::create('driver'));
        foreach ($exchangeList as $exchange) {
            $repeat = $template->getRepeat('panel');
            $repeat->insertText('currSymbol', $exchange->getCurrency());

            $api = $exchange->getApi();
            $balanceList = $api->fetchBalance();
            unset($balanceList['info']);
            unset($balanceList['free']);
            unset($balanceList['used']);
            unset($balanceList['total']);
            //vd($balanceList);
            $total = 0;

            foreach ($balanceList as $code => $bal) {
                $marketId = strtoupper($code) . '/' . $exchange->getCurrency();
                $volume = sprintf('%01.8f', $bal['total']);
                $value = '';
                $bid = '';
                $ask = '';
                $change = '';

                if ($code != $exchange->getCurrency()) {
                    try {
                        $t = $api->fetchTicker($marketId);
                        //$totals[$coin] = $t['bid'] * self::truncateToString($bal['total'],8);
                        $value = $t['ask'] * Exchange::truncateToString($bal['total'], 8);       // I think this reflects a more accurate total
                        $total += $value;

                        $value = sprintf('$%01.2f', $value);

                        $bid = sprintf('%01.4f', $t['bid']);
                        $ask = sprintf('%01.4f', $t['ask']);
                        $change = sprintf('%01.2f', $t['percentage']);
                    } catch (\Exception $e) {
                        \Tk\Log::error($marketId . ' ' . $e->getMessage());
                    }
                }

                $row = $repeat->getRepeat('row');
                $row->insertText('currencyName', $exchange->getMarketName($code));
                $row->insertText('code', $code);

                if ($code == $exchange->getCurrency()) {
                    $total += $volume;
                    $value = '';
                    $bid = '';
                    $ask = '';
                    $row->insertText('volume', '$' . round($volume, 2));
                } else {
                    $row->insertText('volume', $volume);
                    $row->insertHtml('value', $value);
                    $row->insertText('bid', $bid);
                    $row->insertText('ask', $ask);
                    $row->insertText('change', $change.'%');
                    if ($change < 0) {
                        $row->addCss('change', 'tr-dn');
                    } else if ($change > 0) {
                        $row->addCss('change', 'tr-up');
                    }
                }

                $row->appendRepeat();
            }
            $repeat->setAttr('panel', 'data-panel-title', $exchange->getName());
            $repeat->insertText('total', '$'.round($total, 2));

            $repeat->appendRepeat();

        }
        return $template;
    }


    /**
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<div class="">

  <div class="tk-panel" data-panel-icon="fa fa-rebel" var="panel" repeat="panel">
     <div class="tk-panel-title-right">Total: <span var="total">$0.00</span></div>
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Currency</th>
          <th>Code</th>
          <th>Volume</th>
          <th var="currSymbol">AUD</th>
          <th>Sell</th>
          <th>Buy</th>
          <th>% Change</th>
          <th style="width: 40%">Alert</th>
<!--          <th style="width: 50%">24h Chart</th>-->
        </tr>
      </thead>
      <tbody>
        <tr repeat="row" var="row">
          <td var="currencyName">&nbsp;</td>
          <td var="code">&nbsp;</td>
          <td var="volume">&nbsp;</td>
          <td var="value">&nbsp;</td>
          <td var="bid">&nbsp;</td>
          <td var="ask">&nbsp;</td>
          <td var="change">&nbsp;</td>
          
          <td var="alert">
            <a href="#" class="" title="Alert To Buy" choice="alert-buy"><i class="fa fa-exclamation-triangle text-success"></i></a> 
            <a href="#" class="" title="Alert To Sell" choice="alert-sell"><i class="fa fa-exclamation-triangle text-danger"></i></a>
          </td>
          
<!--          <td var="graph">&nbsp;</td>-->
        </tr>
      </tbody>     
    </table>
     
     
  </div>
  
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }

}