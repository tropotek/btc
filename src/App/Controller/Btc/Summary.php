<?php
namespace App\Controller\Btc;

use App\Db\ExchangeMap;
use Bs\Controller\AdminIface;
use ccxt\Exchange;
use Dom\Loader;
use Dom\Template;
use Exception;
use Tk\Alert;
use Tk\Request;
use Tk\Uri;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Summary extends AdminIface
{

    /**
     * @var \App\Db\Exchange
     */
    protected $exchange = null;


    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->setPageTitle('Account Summary');
    }


    /**
     * @param Request $request
     * @param string $exchange
     * @throws Exception
     */
    public function doDefault(Request $request, $exchange)
    {
        $this->exchange = ExchangeMap::create()->findFiltered(array('driver' => $exchange, 'userId' => $this->getConfig()->getAuthUser()->getId()))->current();
        if (!$this->exchange) {
            Alert::addError('Exchange not found!');
            Uri::create()->redirect();
        }
    }

    /**
     * @return Template
     * @throws Exception
     */
    public function show()
    {
        $template = parent::show();

        $this->exchange->getApi()->loadMarkets();
        $balance = $this->exchange->getApi()->fetchBalance();
        $marketList = $balance['total'];

        foreach ($marketList as $market => $amount) {
            if ($amount <= 0) continue;
            $row = $template->getRepeat('row');
            $row->insertText('market', $market);
            $row->insertText('name', $this->exchange->getMarketName($market));
            $row->insertText('amount', Exchange::number_to_string($amount));
            $row->appendRepeat();
        }
        $template->insertText('equity', Exchange::number_to_string($this->exchange->getTotalEquity()) . ' ' . $this->exchange->getCurrency());

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

        return Loader::load($xhtml);
    }

}