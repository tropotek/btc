<?php
namespace App\Controller\Auth;

use Bs\Controller\AdminEditIface;
use Dom\Template;
use Tk\Request;

/**
 * TODO: Add Route to routes.php:
 *      $routes->add('auth-edit', Route::create('/staff/authEdit.html', 'App\Controller\Auth\Edit::doDefault'));
 *
 * @author Mick Mifsud
 * @created 2021-10-22
 * @link http://tropotek.com.au/
 * @license Copyright 2021 Tropotek
 */
class Edit extends AdminEditIface
{

    /**
     * @var \App\Db\Auth
     */
    protected $auth = null;


    /**
     * Iface constructor.
     */
    public function __construct()
    {
        $this->setPageTitle('Auth Edit');
    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function doDefault(Request $request)
    {
        $this->auth = new \App\Db\Auth();
        if ($request->get('authId')) {
            $this->auth = \App\Db\AuthMap::create()->find($request->get('authId'));
        }

        $this->setForm(\App\Form\Auth::create()->setModel($this->auth));
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
<div class="tk-panel" data-panel-title="Auth Edit" data-panel-icon="fa fa-book" var="panel"></div>
HTML;
        return \Dom\Loader::load($xhtml);
    }

}