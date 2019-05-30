<?php
namespace App\Controller\Admin\User;

use Tk\Request;
use Dom\Template;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Profile extends \Bs\Controller\Admin\User\Profile
{



    /**
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     * @param Request $request
     * @throws \Exception
     */
    public function doDefault(Request $request)
    {
        parent::doDefault($request);

    }


    /**
     * @return \Dom\Template
     * @throws \Exception
     */
    public function show()
    {
        $this->getActionPanel()->append(\Tk\Ui\Link::createBtn('Exchanges', \Bs\Uri::createHomeUrl('/exchangeManager.html'), 'fa fa-btc'));
        $template = parent::show();




        return $template;
    }


    /**
     * DomTemplate magic method
     *
     * @return Template
     */
//    public function __makeTemplate()
//    {
//        $xhtml = <<<HTML
//<div>
//
//  <div class="tk-panel" data-panel-icon="fa fa-user" var="form"></div>
//
//</div>
//HTML;
//
//        return \Dom\Loader::load($xhtml);
//    }

}