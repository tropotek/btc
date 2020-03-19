<?php
namespace App\Controller\Admin;

use Tk\Request;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Dashboard extends \Bs\Controller\AdminIface
{

    /**
     * @var \Bs\Table\User
     */
    protected $table = null;


    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->setPageTitle('Dashboard');
        $this->getActionPanel()->setEnabled(false);
        $this->getCrumbs()->reset();
    }


    /**
     * @param Request $request
     * @throws \Exception
     */
    public function doDefault(Request $request)
    {
        $editUrl = \Bs\Uri::createHomeUrl('/userEdit.html');

        $this->table = \Bs\Table\User::create()->setEditUrl($editUrl)->init();
        $this->table->setList($this->table->findList(array()));

    }


    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $template = parent::show();

        $template->appendTemplate('panel', $this->table->show());

        return $template;
    }

    /**
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<div>
  <div class="tk-panel" data-panel-title="System Users" data-panel-icon="fa fa-empire" var="panel"></div>
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }
    
}