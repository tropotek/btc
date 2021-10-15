<?php
namespace App\Controller\Market;

use Bs\Controller\AdminEditIface;
use Dom\Template;
use Tk\Request;

/**
 * TODO: Add Route to routes.php:
 *      $routes->add('market-edit', Route::create('/staff/marketEdit.html', 'App\Controller\Market\Edit::doDefault'));
 *
 * @author Mick Mifsud
 * @created 2021-10-15
 * @link http://tropotek.com.au/
 * @license Copyright 2021 Tropotek
 */
class Edit extends AdminEditIface
{

    /**
     * @var \App\Db\Market
     */
    protected $market = null;


    /**
     * Iface constructor.
     */
    public function __construct()
    {
        $this->setPageTitle('Market Edit');
    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function doDefault(Request $request)
    {
        $this->market = new \App\Db\Market();
        if ($request->get('marketId')) {
            $this->market = \App\Db\MarketMap::create()->find($request->get('marketId'));
        }

        $this->setForm(\App\Form\Market::create()->setModel($this->market));
        $this->initForm($request);
        $this->getForm()->execute();
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
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
<div class="tk-panel" data-panel-title="Market Edit" data-panel-icon="fa fa-money" var="panel"></div>
HTML;
        return \Dom\Loader::load($xhtml);
    }

}