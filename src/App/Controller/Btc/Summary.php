<?php
namespace App\Controller\Btc;

use Tk\Request;
use Dom\Template;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Summary extends \Bs\Controller\AdminIface
{

    /**
     * @var \App\Db\Exchange
     */
    protected $exchange = null;


    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->setPageTitle('Account Summary');
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





    }

    /**
     * @return Template
     * @throws \Exception
     */
    public function show()
    {
        $template = parent::show();

//        $marketList = $this->exchange->getAccountSummary();
//        $marketList[$this->exchange->getCurrency()] = $this->exchange->getAvailableCurrency();

        $this->exchange->getApi()->loadMarkets();
        $balance = $this->exchange->getApi()->fetchBalance();
        $marketList = $balance['total'];

        foreach ($marketList as $market => $amount) {
            if ($amount <= 0) continue;
            $row = $template->getRepeat('row');
            $row->insertText('market', $market);
            $row->insertText('name', $this->exchange->getMarketName($market));
            $row->insertText('amount', \ccxt\Exchange::number_to_string($amount));
            $row->appendRepeat();
        }
        $template->insertText('equity', \ccxt\Exchange::number_to_string($this->exchange->getTotalEquity()));
        vd($marketList);

        
        return $template;
    }


    /**
     * @return Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<div>

  <div class="tk-panel" data-panel-icon="fa fa-list-alt" var="panel">
    
    <table class="table table-bordered table-striped">
      <tr>
        <th>Code</th>
        <th>Name</th>
        <th>Amount</th>
      </tr>
      <tr repeat="row">
        <td var="market"></td>
        <td var="name"></td>
        <td var="amount"></td>
      </tr>
      <tr class="total">
        <th colspan="2">Total (ask):</th>
        <th var="equity"></th>
      </tr>
    </table>
    
  </div>
    
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }

}