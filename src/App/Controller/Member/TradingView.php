<?php
namespace App\Controller\Member;

use App\Db\Exchange;
use App\Db\TickMap;
use Tk\Db\Tool;
use Tk\Request;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class TradingView extends \Bs\Controller\AdminIface
{

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->setPageTitle('TradingView');
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


        return $template;
    }


    /**
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {

        $xhtml = <<<HTML
<div class="">

  <div class="tk-panel" data-panel-icon="fa fa-rebel" var="panel">
    
    <!-- TradingView Widget BEGIN -->
<div class="tradingview-widget-container">
  <div id="tradingview_bfb95" style="height: 700px;"></div>
  <div class="tradingview-widget-copyright"><a href="https://www.tradingview.com/symbols/BTCAUD/?exchange=BINANCE" rel="noopener" target="_blank"><span class="blue-text">BTCAUD Chart</span></a> by TradingView</div>
  <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
  <script type="text/javascript">
  new TradingView.widget(
  {
  "autosize": true,
  "symbol": "BINANCE:BTCAUD",
  //"interval": "60",
  "interval": "1",
  //"timezone": "Etc/UTC",
  "timezone": "Australia/Sydney",
  "theme": "light",
  "style": "1",
  "locale": "en",
  "toolbar_bg": "#f1f3f6",
  "enable_publishing": false,
  "withdateranges": true,
  "hide_side_toolbar": false,
  "allow_symbol_change": true,
  "details": true,
  //"calendar": true,
  "show_popup_button": true,
  "studies": [
    //"ROC@tv-basicstudies",
    //KST@tv-basicstudies
    "RSI@tv-basicstudies"
    
  ],
  "watchlist": [
    "BTCAUD",
    "ETHAUD",
    "XRPAUD",
    "LINKAUD",
    "AUDUSD"
  ],
  "container_id": "tradingview_bfb95"
}
  );
  </script>
</div>
<!-- TradingView Widget END -->
     
  </div>
  
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }

}