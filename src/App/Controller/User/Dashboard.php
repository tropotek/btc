<?php
namespace App\Controller\User;

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
        parent::__construct();
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
     <p>Something spiffy.....</p>
     <p><a href="/logout.html">Logout ;-)</a></p>
  </div>
  
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }

}