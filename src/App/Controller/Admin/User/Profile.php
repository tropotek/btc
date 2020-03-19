<?php
namespace App\Controller\Admin\User;

use Tk\Request;
use Dom\Template;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Profile extends \Bs\Controller\User\Profile
{

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

}