<?php
namespace App\Controller\Asset;

use Bs\Controller\AdminManagerIface;
use Dom\Template;
use Tk\Request;

/**
 * TODO: Add Route to routes.php:
 *      $routes->add('asset-manager', Route::create('/staff/assetManager.html', 'App\Controller\Asset\Manager::doDefault'));
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
        $this->setPageTitle('Asset Manager');
    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function doDefault(Request $request)
    {
        $this->setTable(\App\Table\Asset::create());
        $this->getTable()->setEditUrl(\Bs\Uri::createHomeUrl('/assetEdit.html'));
        $this->getTable()->init();

        $filter = array('userId' => $this->getAuthUser()->getId());
        $this->getTable()->setList($this->getTable()->findList($filter));
    }

    /**
     * Add actions here
     */
    public function initActionPanel()
    {
        $this->getActionPanel()->append(\Tk\Ui\Link::createBtn('New Asset',
            $this->getTable()->getEditUrl(), 'fa fa-btc fa-add-action'));
        $this->getActionPanel()->append(\Tk\Ui\Link::createBtn('Markets',
            \Bs\Uri::createHomeUrl('/marketManager.html'), 'fa fa-money fa-add-action'));
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
<div class="tk-panel" data-panel-title="Assets" data-panel-icon="fa fa-btc" var="panel"></div>
HTML;
        return \Dom\Loader::load($xhtml);
    }

}