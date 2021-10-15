<?php
namespace App\Controller\Market;

use Bs\Controller\AdminManagerIface;
use Dom\Template;
use Tk\Request;

/**
 * TODO: Add Route to routes.php:
 *      $routes->add('market-manager', Route::create('/staff/marketManager.html', 'App\Controller\Market\Manager::doDefault'));
 *
 * @author Mick Mifsud
 * @created 2021-10-15
 * @link http://tropotek.com.au/
 * @license Copyright 2021 Tropotek
 */
class Manager extends AdminManagerIface
{

    /**
     * Manager constructor.
     */
    public function __construct()
    {
        $this->setPageTitle('Market Manager');
    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function doDefault(Request $request)
    {
        $this->setTable(\App\Table\Market::create());
        $this->getTable()->setEditUrl(\Bs\Uri::createHomeUrl('/marketEdit.html'));
        $this->getTable()->init();

        $filter = array();
        $this->getTable()->setList($this->getTable()->findList($filter));
    }

    /**
     * Add actions here
     */
    public function initActionPanel()
    {
        $this->getActionPanel()->append(\Tk\Ui\Link::createBtn('New Market',
            $this->getTable()->getEditUrl(), 'fa fa-money fa-add-action'));
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $this->initActionPanel();
        $template = parent::show();

        $template->appendTemplate('panel', $this->getTable()->show());

        return $template;
    }

    /**
     * @return Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<div class="tk-panel" data-panel-title="Markets" data-panel-icon="fa fa-money" var="panel"></div>
HTML;
        return \Dom\Loader::load($xhtml);
    }

}