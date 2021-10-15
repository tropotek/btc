<?php
namespace App\Controller\Asset;

use Bs\Controller\AdminEditIface;
use Dom\Template;
use Tk\Request;

/**
 * TODO: Add Route to routes.php:
 *      $routes->add('asset-edit', Route::create('/staff/assetEdit.html', 'App\Controller\Asset\Edit::doDefault'));
 *
 * @author Mick Mifsud
 * @created 2021-10-15
 * @link http://tropotek.com.au/
 * @license Copyright 2021 Tropotek
 */
class Edit extends AdminEditIface
{

    /**
     * @var \App\Db\Asset
     */
    protected $asset = null;


    /**
     * Iface constructor.
     */
    public function __construct()
    {
        $this->setPageTitle('Asset Edit');
    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function doDefault(Request $request)
    {
        $this->asset = new \App\Db\Asset();
        if ($request->get('assetId')) {
            $this->asset = \App\Db\AssetMap::create()->find($request->get('assetId'));
        }

        $this->setForm(\App\Form\Asset::create()->setModel($this->asset));
        $this->initForm($request);
        $this->getForm()->execute();
    }
    /**
     * Add actions here
     */
    public function initActionPanel()
    {
        $this->getActionPanel()->append(\Tk\Ui\Link::createBtn('Add Market',
            \Bs\Uri::createHomeUrl('/marketEdit.html'), 'fa fa-money fa-add-action'));
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $this->initActionPanel();
        $template = parent::show();

        // Render the form
        $template->appendTemplate('panel', $this->getForm()->show());

        return $template;
    }

    /**
     * @return Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<div class="tk-panel" data-panel-title="Asset Edit" data-panel-icon="fa fa-btc" var="panel"></div>
HTML;
        return \Dom\Loader::load($xhtml);
    }

}