<?php
namespace App\Controller\Member;

use Bs\Controller\ManagerTrait;
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



    public function calculateTotal()
    {
        $list = \App\Db\AssetMap::create()->findFiltered(['userId' => $this->getAuthUser()->getId()]);
        $total = 0;
        foreach ($list as $asset) {
            $t = round($asset->getMarketTotalValue(), 2);
            $total = $total + $t;
        }
        return $total;
    }




    public function show()
    {
        $template = parent::show();

        $template->insertText('total', '$' . number_format($this->calculateTotal(), 2));

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
    <div class="row text-center">
      <div class="col-md-12"><strong>Total:</strong> <span var="total">$0.00</span></div>
    </div>
  </div>

  <div class="tk-panel" data-panel-icon="fa fa-bar-chart" var="panel" repeat1="panel">
     
  </div>
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }

}